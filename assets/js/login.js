import  $ from 'jquery';
import 'popper.js';
import 'bootstrap';
import '../css/login.css';

document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});

$('.dropdown-toggle').dropdown();
