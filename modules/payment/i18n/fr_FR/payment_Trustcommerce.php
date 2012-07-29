<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'decline' => array
	(
		'avs' => 'Le Service de V�rification d\'Adresse (AVS) a retourn� une erreur. L\'adresse entr�e ne correspond pas � l\'adresse de facturation du fichier bancaire.',
		'cvv' => 'Le code de v�rification (CVV) de votre carte n\'a pas �t� accept�. Le num�ro que vous avez entr� n\'est pas le bon ou ne correspond pas � cette carte.',
		'call' => 'La carte doit �tre autoris�e par t�l�phone. Vous devez choisir ce num�ro d\'appel parmis ceux list�s sur la carte et demander un code d\'authentification hors ligne (offline authcode). Celui-ci pourra ensuite �tre entr� dans le champ r�serv� � cet effet (offlineauthcode).',
		'expiredcard' => 'La carte a expir�e. Vous devez obtenir une carte poss�dant une date de validit� valide aupr�s du fournisseur de celle-ci.',
		'carderror' => 'Le num�ro de carte est invalide. Veuillez v�rifier que vous avez correctement entr� le num�ro, ou que cette carte n\'ait pas �t� report�e comme �tant vol�e.',
		'authexpired' => 'Tentative d\'autoriser une pr�-autorisation qui a expir�e il y a plus de 14 jours..',
		'fraud' => 'Le score de v�rification est en dessous du score anti-fraude CrediGuard.',
		'blacklist' => 'CrediGuard donne cette valeur comme �tant sur liste noire (blacklist�e).',
		'velocity' => 'Le seuil de cont�le CrediGuard a �t� atteint. Trop de transactions ont �t� effectu�s.',
		'dailylimit' => 'La limite journali�re des transactions de cette carte a �t� atteinte.',
		'weeklylimit' => 'La limite hebdomadaire des transactions de cette carte a �t� atteinte.',
		'monthlylimit' => 'La limite mensuelle des transactions de cette carte a �t� atteinte.',
	),
	'baddata' => array
	(
		'missingfields' => 'Un ou plusieurs param�tres requis pour ce type de transaction n\'a pas �t� transmis.',
		'extrafields' => 'Des param�tres interdits pour ce type de transaction ont �t� envoy�s.',
		'badformat' => 'Un champ n\'a pas �t� format� correctement, comme par exemple des caract�res alphab�tiques ins�r�s dans un champ num�rique.',
		'badlength' => 'Un champ est plus grand ou plus petit que la taille accept�e par le serveur.',
		'merchantcantaccept' => 'Le commer�ant ne peut accepter les donn�es pass�es dans ce champ.',
		'mismatch' => 'Les donn�es de l\'un des champs erron� ne correspond pas avec l\'autre champs.',
	),
	'error' => array
	(
		'cantconnect' => 'Impossible de se connecter � la plateforme TrustCommerce ! Veuillez vous assurer que votre connexion internet fonctionne.',
		'dnsfailure' => 'Le logiciel TCLink a �t� incapable de r�soudre l\'adresse DNS du serveur. Assurez-vous que votre machine poss�de la capacit� de r�soudre les noms DNS.',
		'linkfailure' => 'La connexion n\'a pas pu �tre �tablie et vous avez �t� d�connect� avant que la transaction ne soit compl�te.',
		'failtoprocess' => 'Les serveurs bancaires ne sont pas disponibles actuellement et ne peuvent donc accepter des transactions. Veuillez r�essayer dans quelques minutes. Vous pouvez �galement tester avec une autre carte d\'un autre organisme bancaire.',
	)
);
