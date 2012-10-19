(function($) {

$.fn.tokenInput = function (url, options) {
    var settings = $.extend({
        url: url,
        hintText: "Type in a search term",
        noResultsText: "No results",
        searchingText: "Searching...",
        searchDelay: 300
    }, options);

    settings.classes = $.extend({
        tokenList: "token-input-list",
        token: "token-input-token",
        tokenDelete: "token-input-delete-token",
        selectedToken: "token-input-selected-token",
        highlightedToken: "token-input-highlighted-token",
        dropdown: "token-input-dropdown",
        dropdownItem: "token-input-dropdown-item",
        dropdownItem2: "token-input-dropdown-item2",
        selectedDropdownItem: "token-input-selected-dropdown-item",
        inputToken: "token-input-input-token"
    }, options.classes);

    return this.each(function () {
      if ( ! $(this).hasClass('token-initialized')) {
        var list = new $.TokenList(this, settings);        
        $(this).addClass('token-initialized');
      }
    });
};

$.TokenList = function (input, settings) {
    //
    // Variables
    //

    // Input box position "enum"
    var POSITION = {
        BEFORE: 0,
        AFTER: 1,
        END: 2
    };
    
    // Keys "enum"
    var KEY = {
        BACKSPACE: 8,
        RETURN: 13,
        LEFT: 37,
        UP: 38,
        RIGHT: 39,
        DOWN: 40
    };
    
    // Save the tokens
    var saved_tokens = [];
    
    // Basic cache to save on db hits
    var cache = new $.TokenList.Cache();

    // Keep track of the timeout
    var timeout;
    
    // Create a new text input an attach keyup events
    var input_box = $("<input type=\"text\">")
        .css({
            outline: "none"
        })
        .focus(function () {
            show_dropdown_hint();
        })
        .blur(function () {
            if(!selected_dropdown_item) {
                hide_dropdown();
            }
        })
        .keydown(function (event) {
            var previous_token;
            var next_token;

            switch(event.keyCode) {
                case KEY.LEFT:
                case KEY.RIGHT:
                case KEY.UP:
                case KEY.DOWN:
                    if(!$(this).val()) {
                        previous_token = input_token.prev();
                        next_token = input_token.next();
                        
                        if((previous_token.length && previous_token.get(0) === selected_token) || (next_token.length && next_token.get(0) === selected_token)) {
                            // Check if there is a previous/next token and it is selected
                            if(event.keyCode == KEY.LEFT || event.keyCode == KEY.UP) {
                                deselect_token($(selected_token), POSITION.BEFORE);
                            } else {
                                deselect_token($(selected_token), POSITION.AFTER);
                            }
                        } else if((event.keyCode == KEY.LEFT || event.keyCode == KEY.UP) && previous_token.length) {
                            // We are moving left, select the previous token if it exists
                            select_token($(previous_token.get(0)));
                        } else if((event.keyCode == KEY.RIGHT || event.keyCode == KEY.DOWN) && next_token.length) {
                            // We are moving right, select the next token if it exists
                            select_token($(next_token.get(0)));
                        }
                    } else {
                        var dropdown_item = null;

                        if(event.keyCode == KEY.DOWN || event.keyCode == KEY.RIGHT) {
                            dropdown_item = $(selected_dropdown_item).next();
                        } else { 
                            dropdown_item = $(selected_dropdown_item).prev();                
                        }

                        if(dropdown_item.length) {
                            select_dropdown_item(dropdown_item);
                        }
                        return false;
                    }
                    break;
                
                case KEY.BACKSPACE:
                    previous_token = input_token.prev();

                    if(!$(this).val().length) {
                        if(selected_token) {
                            delete_token($(selected_token));
                        } else if(previous_token.length) {
                            select_token($(previous_token.get(0)));
                        }
                        
                        return false;
                    } else if($(this).val().length == 1) {
                        hide_dropdown();
                    } else {
                        show_dropdown_searching();
                        do_search(1);
                    }
                    break;

                case KEY.RETURN:
                    if(selected_dropdown_item) {
                        add_token($(selected_dropdown_item));
                        return false;
                    }
                    break;

                default:
                    if(is_printable_character(event.keyCode)) {
                        show_dropdown_searching();
                        clearTimeout(timeout);
    				            timeout = setTimeout(do_search, settings.searchDelay);
				            }
				    
                    break;
            }
        });

    // Keep a reference to the original input box
    var hidden_input = $(input)
                           .hide()
                           .focus(function () {
                               input_box.focus();
                           })
                           .blur(function () {
                               input_box.blur();
                           })
                           .val("");

    // Keep a reference to the selected token and dropdown item
    var selected_token = null;
    var selected_dropdown_item = null;
    
    // The list to store the token items in
    var token_list = $("<ul />")
        .addClass(settings.classes.tokenList)
        .insertAfter(hidden_input)
        .click(function (event) {
            var li = get_element_from_event(event, "li");
            if(li && li.get(0) != input_token.get(0)) {
                toggle_select_token(li);
                return false;
            } else {
                input_box.focus();
            
                if(selected_token) {
                    deselect_token($(selected_token), POSITION.END);
                }
            }
        })
        .mouseover(function (event) {
            var li = get_element_from_event(event, "li");
            if(li && selected_token !== this) {
                li.addClass(settings.classes.highlightedToken);
            }
        })
        .mouseout(function (event) {
            var li = get_element_from_event(event, "li");
            if(li && selected_token !== this) {
                li.removeClass(settings.classes.highlightedToken);
            }
        })
        .mousedown(function (event) {
            // Stop user selecting text on tokens
            var li = get_element_from_event(event, "li");
            if(li){
                return false;
            }
        });


    // The list to store the dropdown items in
    var dropdown = $("<div>")
        .addClass(settings.classes.dropdown)
        .insertAfter(token_list)
        .hide();
    
    // The token holding the input box
    var input_token = $("<li />")
        .addClass(settings.classes.inputToken)
        .appendTo(token_list)
        .append(input_box);

    //
    // Functions
    //

    function is_printable_character(keycode) {
        if((keycode >= 48 && keycode <= 90) ||      // 0-1a-z
           (keycode >= 96 && keycode <= 111) ||     // numpad 0-9 + - / * .
           (keycode >= 186 && keycode <= 192) ||    // ; = , - . / ^
           (keycode >= 219 && keycode <= 222)       // ( \ ) '
          ) {
              return true;
          } else {
              return false;
          }
    }

    // Get an element of a particular type from an event (click/mouseover etc)
    function get_element_from_event (event, element_type) {
        var target = $(event.target);
        var element = null;

        if(target.is(element_type)) {
            element = target;
        } else if(target.parent(element_type).length) {
            element = target.parent(element_type+":first");
        }

        return element;
    }
    
    // Add a token to the token list
    function add_token (item) {
        var li_data = $.data(item.get(0), "tokeninput");
        var this_token = $("<li><p>"+ li_data.name +"</p> </li>")
            .addClass(settings.classes.token)
            .insertBefore(input_token);
             
        $("<span>x</span>")
            .addClass(settings.classes.tokenDelete)
            .appendTo(this_token)
            .click(function () {
                delete_token($(this).parent());
                return false;
            });
        
        $.data(this_token.get(0), "tokeninput", {"id": li_data.id, "name": li_data.name});

        // Clear input box and make sure it keeps focus
        input_box
            .val("")
            .focus();        
        
        // Don't show the help dropdown, they've got the idea
        hide_dropdown();

        // Save this token id
        var id_string = li_data.id + ","
        hidden_input.val(hidden_input.val() + id_string);
    }

    // Select a token in the token list
    function select_token (token) {
        token.addClass(settings.classes.selectedToken);
        selected_token = token.get(0);

        // Hide input box
        input_box.val("");
        
        // Hide dropdown if it is visible (eg if we clicked to select token)
        hide_dropdown();
    }

    // Deselect a token in the token list
    function deselect_token (token, position) {
        token.removeClass(settings.classes.selectedToken);
        selected_token = null;

        if(position == POSITION.BEFORE) {
            input_token.insertBefore(token);
        } else if(position == POSITION.AFTER) {
            input_token.insertAfter(token);
        } else {
            input_token.appendTo(token_list);
        }

        // Show the input box and give it focus again
        input_box.focus();
    }
    
    // Toggle selection of a token in the token list
    function toggle_select_token (token) {
        if(selected_token == token.get(0)) {
            deselect_token(token, POSITION.END);
        } else {
            if(selected_token) {
                deselect_token($(selected_token), POSITION.END);
            }
            select_token(token);
        }
    }
    
    // Delete a token from the token list
    function delete_token (token) {
        // Remove the id from the saved list
        var token_data = $.data(token.get(0), "tokeninput");
        //saved_tokens.splice($.inArray(saved_tokens, token_data.id), 1);

        // Delete the token
        token.remove();
        selected_token = null;

        // Show the input box and give it focus again
        input_box.focus();

        // Delete this token's id from hidden input
        var str = hidden_input.val()
        var start = str.indexOf(token_data.id+",");
        var end = str.indexOf(",", start) + 1;

        if(end >= str.length) {
            hidden_input.val(str.slice(0, start));
        } else {
            hidden_input.val(str.slice(0, start) + str.slice(end, str.length));
        }
    }

    // Hide and clear the results dropdown
    function hide_dropdown () {
        dropdown.hide().empty();
        selected_dropdown_item = null;
    }
    
    function show_dropdown_searching () {
        dropdown
            .html("<p>"+settings.searchingText+"</p>")
            .show();
    }
    
    function show_dropdown_hint () {
        dropdown
            .html("<p>"+settings.hintText+"</p>")
            .show();
    }

    // Highlight the query part of the search term
	function highlight_term(value, term) {
		return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<em>$1</em>");
	}

    // Populate the results dropdown with some results
    function populate_dropdown (query, results) {
        if(results != null && results.length) {
            dropdown.empty();
            var dropdown_ul = $("<ul>")
                .appendTo(dropdown)
                .mouseover(function (event) {
                    select_dropdown_item(get_element_from_event(event, "li"));
                })
                .click(function (event) {
                    add_token(get_element_from_event(event, "li"));
                })
                .mousedown(function (event) {
                    // Stop user selecting text on tokens
                    return false;
                })
                .hide();

            for(var i in results) {
                if (results.hasOwnProperty(i)) {
                    var this_li = $("<li>"+highlight_term(results[i].name, query)+"</li>")
                                      .appendTo(dropdown_ul);
                
                    if(i%2) {
                        this_li.addClass(settings.classes.dropdownItem);
                    } else {
                        this_li.addClass(settings.classes.dropdownItem2);
                    }
                
                    if(i == 0) {
                        select_dropdown_item(this_li);
                    }
        
                    $.data(this_li.get(0), "tokeninput", {"id": results[i].id, "name": results[i].name});
                }
            }

            dropdown.show();
            dropdown_ul.slideDown("fast");

        } else {
            dropdown
                .html("<p>"+settings.noResultsText+"</p>")
                .show();
        }
    }
    
    // Highlight an item in the results dropdown
    function select_dropdown_item (item) {
        if(item) {
            if(selected_dropdown_item) {
                deselect_dropdown_item($(selected_dropdown_item));
            }
        
            item.addClass(settings.classes.selectedDropdownItem);
            selected_dropdown_item = item.get(0);
        }
    }
    
    // Remove highlighting from an item in the results dropdown
    function deselect_dropdown_item (item) {
        item.removeClass(settings.classes.selectedDropdownItem);
        selected_dropdown_item = null;
    }

    // Do a search
    function do_search(trim_last_char) {
        var query = input_box.val().toLowerCase();
        
        if(trim_last_char == 1) {
            query = query.substring(0, query.length-1);
        }

        if(query && query.length) {
            if(selected_token) {
                deselect_token($(selected_token), POSITION.AFTER);
            }
            
            var cached_results = cache.get(query);
            // cached_results = [{"id":1,"name":"Blake Lucchesi"},{"id":4,"name":"Leonidas Lucchesi"},{"id":7,"name":"Kyle Holmes"},
            // {"id":8,"name":"Andre Ramirez"},{"id":9,"name":"Kyle Varga"},{"id":10,"name":"Jason Brothers"},{"id"
            // :11,"name":"Sean McGuinnes"},{"id":12,"name":"Omar Sandoval"},{"id":13,"name":"sample Arevalo"}];
            if(cached_results) {
                populate_dropdown(query, cached_results);
            } else {
                $.get(settings.url, {"q": query }, function (results) {
                    cache.add(query, results);
                    populate_dropdown(query, results);
                }, "json");
            }
        }
    }
};

// Really basic cache for the results
$.TokenList.Cache = function (options) {
    var settings = $.extend({
        max_size: 10
    }, options);
    
    var data = {};
    var size = 0;

    var flush = function () {
        data = {};
        size = 0;
    };

    this.add = function (query, results) {
        if(size > settings.max_size) {
            flush();
        }
        
        if(!data[query]) {
            size++;
        }
        
        data[query] = results;
    };
    
    this.get = function (query) {
        return data[query];
    };
};

})(jQuery);
