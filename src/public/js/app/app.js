'use strict';

var redisApp = angular.module('redisApp', []);

var AppSettings = {
    statusPollTiming: 6000
};

//service style, probably the simplest one
redisApp.service('dataService', ['$rootScope', '$http', function($rootScope, $http) {
    var thisService = this;
    // Set Some Defaults
    $rootScope.status = {};

    this.status = $rootScope.status;

    this.updateStatus = function(){
        $http.get('/api/status').success(function(data){
            thisService.status = data;
            $rootScope.$broadcast('status-update', data);

            setTimeout(function(){
                thisService.updateStatus();
            }, AppSettings.statusPollTiming);
        }).error(function(data){
            console.log("Error getting status!", data);

            setTimeout(function(){
                thisService.updateStatus();
            }, AppSettings.statusPollTiming);
        });
    }

    this.testRoot = function(){
        console.log('rootScope', $rootScope);
        console.log('rootScope.testing', $rootScope.testing);
    };

    this.updateStatus();
}]);

function MenuCtrl($scope, $rootScope, $http, dataService) {
    /* Internal Variables */
    var currentMode = '';

    /* Get Common Elements */
    var elUserCpu = $('#userCpu');
    var elSysCpu = $('#sysCpu');
    var elUserMemory = $('#userMemory');

    /* Internal Methods */
    var Init = function()
    {
        /* Get the current Mode */
        getCurrentMode(function(modeName){
            $('#MenuCtrl li.mode_' + modeName).addClass('active');
        });
    };

    var switchTabs = function(modeName){
        $('#MenuCtrl li').removeClass('active');
        $http.post('/api/mode', { mode: modeName }).success(function(){
            currentMode = modeName
            $('#MenuCtrl li.mode_' + modeName).addClass('active');
        }).error(function(data){
            alert('There was a problem switching');
            console.log('data error', data);
        });
    };

    var getCurrentMode = function(callback)
    {
        $http.get('/api/mode').success(function(data){
            currentMode = data.mode;
            callback(currentMode);
        }).error(function(data){
            currentMode = 'IdleMode';
            callback(currentMode);
        });
    };

    /* Events */
    $scope.menuClick = function(modeName){
        if(modeName == currentMode)
        {
            return; // do nothing
        }

        switchTabs(modeName);
    };

    /* Status Updates */
    $scope.$on('status-update', function(event, data){
        $rootScope.status = data;
        elUserCpu.css('width', data.userCpu.toString() + '%');
        elSysCpu.css('width', data.sysCpu.toString() + '%');

        elUserMemory.css('width', data.userMemory.toString() + '%');
    });

    /* Init */
    Init();
}