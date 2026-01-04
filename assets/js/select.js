/*$(document).ready(function() {
    $('select[multiple]').select2();
});*/


// Fonction d'initialisation de Select2
function initSelect2() {
    $('select[multiple]').select2({
        placeholder: 'Sélectionnez un ou plusieurs membres',
        allowClear: true,
        width: '100%'
    });
}

// Initialisation au chargement complet de la page (premier accès)
$(document).ready(function() {
    initSelect2();
});

// Initialisation lors des navigations Turbo (navigation sans rechargement)
document.addEventListener('turbo:load', function() {
    initSelect2();
});

// Réinitialisation lors du rendu Turbo (pour les Turbo Frames)
document.addEventListener('turbo:render', function() {
    initSelect2();
});
