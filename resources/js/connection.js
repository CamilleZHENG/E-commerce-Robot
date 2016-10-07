'use strict';
var $identifiant, $password;

$(document).ready(function(){

	$('form').on('submit', validerFormulaire);

});

function validerFormulaire(evenement)
{
	evenement.preventDefault();

	$('.message_error').hide();
	$('input').removeClass('error');

	$identifiant = $('[name="identifiant"]');
	$motDePass 	 = $('[name="code"]');


	$.post('traitement.php',
		{identifiant : $identifiant.val()},
			function(reponse)/*fonction pour la réponse de serveur,
			"reponse" : nom de variable */
			{
				if(reponse === 'notExist')
				{
					$identifiant.addClass('error');
					$('[data-error="nb-car-id-check"]').show();
				}

			});












}