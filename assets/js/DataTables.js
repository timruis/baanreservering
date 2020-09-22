import  $ from 'jquery';
import '../css/DataTables.css';
import 'popper.js';
import 'bootstrap';
import 'datatables.net-bs4';


$(document).ready(function($) {
    $('#bootstrap-data-table').DataTable({
        lengthMenu: [[10, 20, 50], [10, 20, 50]],
    });


    $('#bootstrap-data-table-export').DataTable({
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
    });

    $('.delete-button').on('click',function (){
        var Link = $(this).data("link");
        $("#modal-link").attr("href", Link);
    })
});

