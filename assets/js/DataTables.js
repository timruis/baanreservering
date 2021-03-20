import  $ from 'jquery';
import '../css/DataTables.css';
import 'popper.js';
import 'bootstrap';
import 'datatables.net-bs4';
import 'datatables.net-buttons';
import 'datatables.net-buttons-bs4';
import 'datatables.net-select';
import 'datatables.net-select-bs4';
import 'datatables.net-buttons/js/buttons.html5.js';
import 'datatables.net-buttons/js/buttons.colVis.js';
import 'datatables.net-buttons/js/dataTables.buttons.js';
import "datatables.net-buttons/js/buttons.print.js";

$(document).ready(function($) {
    $('#bootstrap-data-table').DataTable({
        lengthMenu: [ [-1,10, 25, 50 ], ["All",10, 25, 50 ] ],
    });
    $('.bootstrap-data-table').DataTable({
        lengthMenu: [ [-1,10, 25, 50 ], ["All",10, 25, 50 ] ],
    });
    $('#bootstrap-data-table-export-local-usertracker').DataTable( {
        lengthMenu: [ [-1,10, 25, 50 ], ["All",10, 25, 50 ] ],
        dom: 'Bfrtip',
        buttons: [
            'pageLength',
            {
                extend: 'colvis',
                columnText: function ( dt, idx, title ) {
                    return (idx+1)+': '+title;
                }
            },
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        select: true,
        ajax: "/priviliged/usertracker/list",
    } );

    $('#bootstrap-data-table-export-local').DataTable( {
        lengthMenu: [ [-1,10, 25, 50 ], ["All",10, 25, 50 ] ],
        dom: 'Bfrtip',
        buttons: [
            'pageLength',
            {
                extend: 'colvis',
                columnText: function ( dt, idx, title ) {
                    return (idx+1)+': '+title;
                }
            },
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        select: true,
        ajax: "/priviliged/Members/list",
    } );

    $('#bootstrap-data-table-export').DataTable( {
        lengthMenu:  [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
        dom: 'Bfrtip',
        select: true,
        buttons: ['copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5' ],
    } );
    $('.delete-button').on('click',function (){
        var Link = $(this).data("link");
        $("#modal-link").attr("href", Link);
    })


    setTimeout(() => {  memberManagement() }, 2000);
});



function memberManagement(){

    $('.payed').on('click', function (e) {
        var userid = $(this).val();
        $.post("/admin/check/payment", {id: userid});
        var item = $("#haspayed"+userid);
        if (item.html() === " nee ") {
            item.html(" ja ") ;
        }else if (item.html() === " ja "){
            item.html(" nee ");
        }
    });

    $('.Active').on('click', function (e) {
        var userid = $(this).val();
        $.post("/admin/check/active", {id: userid});
        $(".tbrw"+userid).toggleClass("yellow");
        var item = $("#hasActivated"+userid);
        if (item.html() === " nee ") {
            item.html(" ja ");
        }else if (item.html() === " ja "){
            item.html(" nee ");
        }
    });
    $('.Summer').on('click', function (e) {
        var userid = $(this).val();
        $.post("/admin/check/Summermembership", {id: userid});
        var item = $("#hasSummerMember"+userid);
        if (item.html() === " nee ") {
            item.html(" ja ");
        }else if (item.html() === " ja "){
            item.html(" nee ");
        }
    });
    $('.Winter').on('click', function (e) {
        var userid = $(this).val();
        $.post("/admin/check/Wintermembership", {id: userid});
        var item = $("#hasWinterMember"+userid);
        if (item.html() === " nee ") {
            item.html(" ja ");
        }else if (item.html() === " ja "){
            item.html(" nee ");
        }
    });

    $('.IsNotActive').parents("tr").addClass('yellow');
    $('.Active').each(function() {
        var userid = $(this).val();
        $( this ).parents("tr").addClass('tbrw'+userid);
    });

    $('.DeletePlayer').on('click', function (e) {
        // convert target (e.g. the button) to jquery object
        var $target = $(e.target);

        // modal targeted by the button
        var modalSelector = $target.data('target');
        // retrieve the dom element corresponding to current attribute
        var $modalAttribute = $(modalSelector + ' #modal-link');
        var dataValue = $target.data('link');
        $modalAttribute.attr('href', dataValue);
    });
}