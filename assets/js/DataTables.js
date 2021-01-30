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
        lengthMenu: [[10, 20, 50], [10, 20, 50]],
    });
    $('#bootstrap-data-table-export-local').DataTable( {
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        select: true,
        ajax: "/priviliged/Members/list",

    } );
    $(this).closest("tr").addClass("current");

    $('#bootstrap-data-table-export').DataTable( {
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
});

