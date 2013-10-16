'use strict';

/* Controllers */

angular.module('redisApp.controllers', [])
.controller('MenuCtrl', function($scope) {
    $scope.currentItem
    $scope.menuClick = function(modeName)
    {
        alert(modeName);
    };
})
.controller('PanelCtrl', [function() {

}]);


