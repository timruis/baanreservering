import  $ from 'jquery';
import 'popper.js';
import 'bootstrap';
import 'node-waves';
import 'material-icons';
import "jquery-ui-bundle";
import 'materialize';
import 'animate.css';
import '../css/app.css';
import 'bootstrap-colorpicker';
import 'dropzone';
import 'jquery.dropzone';


$.noConflict();

$(document).ready(function($) {
    "use strict";
    $('.js-datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        yearRange: "1900:"+(new Date).getFullYear(),
        dateFormat: 'yy-mm-dd' }).val();

    [].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {
        new SelectFx(el);
    } );
    $('.selectpicker').selectpicker;

    $("#file-upload-vin").dropzone({
        maxFilesize: 2, // MB
        capture: 'image/*',
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        addRemoveLinks: true,
        dictRemoveFile: 'Remover foto',
    });
    $('#menuToggle').on('click', function(event) {
        $('body').toggleClass('open');
    });

    $('.search-trigger').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        $('.search-trigger').parent('.header-left').addClass('open');
    });

    $('.search-close').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        $('.search-trigger').parent('.header-left').removeClass('open');
    });
    $('.dropdown-toggle').dropdown()
});

