<?php
$route->any('/page', function (){
//   pre('before all 1', 1);
   pre(app('route')->getRoutes(), 1);
}, ['continue' => true]);

$route->any('/page', function (){
    pre('before all 2', 2);
}, ['ajaxOnly' => true]);

$route->any('/page', function (){
    pre('before all 3', 3);
});

$route->any('/page', function (){
    pre('before all 4', 4);
});

$route->use(function (){
    pre('form use :D', 4);
//    pre(app('route')->getRoutes(), 4);

});

$route->before('/*!admin', function (){
    pre('All except admin', 4);
});
$route->before('/admin|page', function (){
    pre('Before admin and page only ', 4);
});
