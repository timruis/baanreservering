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
import Picker from 'pickerjs';

$.noConflict();

$(document).ready(function($) {
    "use strict";
    makePicker('timepickerStart');
    makePicker('timepickerEnd');
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

function makePicker(string) {
    var elements = document.getElementsByClassName(string);
    if (elements.length > 0) {
        var dynamic = document.createElement("div");
        dynamic.innerHTML = "<div class='"+string+"Container'></div>";
        // just change the first, as you did in your post
        elements[0].parentNode.insertBefore(dynamic, elements[0].nextSibling);
    }
    return new Picker(document.querySelector('.'+string), {
        headers:true,
        format: 'mm:ss.SSS ',
        controls: true,
        rows:5,
        increment: {
            minute: 1,
            second: 1,
            millisecond: 10,
        },
    });
}

