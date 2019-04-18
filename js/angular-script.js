angular.module('wp', ['ngRoute', 'ui.bootstrap', 'ngAnimate'])
.config(function($routeProvider, $locationProvider) {
    $routeProvider
    .when('/', {
        templateUrl: localized.partials + '/showRooms.html',
        controller: 'Main'
    })
})
.controller('Main', function($scope, $http, $routeParams) {
   console.log('hi');
   $scope.oneAtATime = true;

   $scope.groups = [
     {
       title: 'Dynamic Group Header - 1',
       content: 'Dynamic Group Body - 1sdfas'
     },
     {
       title: 'Dynamic Group Header - 2',
       content: 'Dynamic Group Body - 2asdfsd'
     }
   ];
 
   $scope.items = ['Item 1', 'Item 2', 'Item 3'];
 
   $scope.addItem = function() {
     var newItemNo = $scope.items.length + 1;
     $scope.items.push('Item ' + newItemNo);
   };
 
   $scope.status = {
     isCustomHeaderOpen: false,
     isFirstOpen: true,
     isFirstDisabled: false
   };
   $http.post( localized.path + '/wp-json/dsol-booking/v1/test', {name:'David '}).then((res)=>{
       console.log(res);
   }, (err)=>{
       console.log(err);
   })
});