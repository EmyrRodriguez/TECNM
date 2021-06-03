var app = angular.module('myApp', []);

app.controller('myCtrl', function ($scope) {
    $scope.firstName = "John";
    $scope.lastName = "Doe";
});

app.directive("w3TestDirective", function () {
    return {
        template: "I was made in a directive constructor!"
    };
});

app.controller('videosCtrl', function ($scope, $http) {
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $scope.evento = 1;
    $scope.area = "P1";
    var InData = {evento: $scope.evento, area: $scope.area};
    $http.post("../controller/videoController.php",InData
    ).then(function (response) {
        $scope.myData = response.data.data;
    });
});