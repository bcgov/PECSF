/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// setInterval(function() {

//     if(($(".main-sidebar").height() - $(".content-wrapper").height()) > 40){
//         $(".content-wrapper").css("min-height", $(".main-sidebar").height()+"px");
//         $(".main-sidebar").css("min-height", $(".content-wrapper").height()+40+"px");
//     }



// },100);

$("button").click(function(){
    $(this).blur();
});
$(".btn").click(function(){
    $(this).blur();
});



