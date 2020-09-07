import  $ from 'jquery';
import '../css/DataTables.css';
import 'popper.js';
import 'bootstrap';
import 'bootstrap';
import 'datatables.net-bs4';
import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfMake.vfs = pdfFonts.pdfMake.vfs;

import * as JSZip from "jszip";
window.JSZip = JSZip;

import "datatables.net-buttons";
import "datatables.net-buttons/js/buttons.html5.js";
import "datatables.net-buttons/js/buttons.print.js";

$(document).ready(function($) {
    var table = $('#bootstrap-data-table-export').DataTable({
        lengthMenu: [[ 10, 25, 50,-1], [10, 25, 50,"All"]],
    });
    new $.fn.dataTable.Buttons( table, {
        buttons: [
            'copy', 'excel', 'pdf'
        ]
    } );

    table.buttons().container()
        .appendTo( $('#bootstrap-data-table-export_wrapper:eq(0)', table.table().container() ) );

    $('#bootstrap-data-table').DataTable({
        lengthMenu: [[10, 20, 50], [10, 20, 50]],
    });
    function ModalDelete(Link) {
        var modalSelector = "#deleteLinkBtn";
        $("#deleteLinkBtn").attr("href", Link);
    }
});

