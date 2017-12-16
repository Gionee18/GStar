

function back() {
	return {
		restrict: 'E',
		scope: false,
		templateUrl: 'templates/admin/back-button.html',
		transclude: true,
		controller: function ($scope) {
			$scope.goBack = function () {
				window.history.back();
			}
		},
	};
};
function bsNavbar($window, $location) {

	var defaults = this.defaults = {
		activeClass: 'active',
		routeAttr: 'data-match-route',
		strict: true
	};

	return {
		restrict: 'A',
		link: function postLink(scope, element, attr, controller) {

			// Directive options
			var options = angular.copy(defaults);
			angular.forEach(Object.keys(defaults), function (key) {
				if (angular.isDefined(attr[key])) options[key] = attr[key];
			});

			// Watch for the $location
			scope.$watch(function () {

				return $location.path();

			}, function (newValue, oldValue) {

				var liElements = element[0].querySelectorAll('li[' + options.routeAttr + '],li > a[' + options.routeAttr + ']');

				angular.forEach(liElements, function (li) {

					var liElement = angular.element(li);
					var pattern = liElement.attr(options.routeAttr).replace('/', '\\/');
					if (options.strict) {
						pattern = '^' + pattern;
					}
					var regexp = new RegExp(pattern, ['i']);

					if (regexp.test(newValue)) {
						liElement.addClass(options.activeClass);
					} else {
						liElement.removeClass(options.activeClass);
					}

				});

				// Close all other opened elements
				var op = $('#sidebar-nav').find('.open:not(.active)');
				op.children('.submenu').slideUp('fast');
				op.removeClass('open');
			});

		}

	};
}

function gd(year, day, month) {
	return new Date(year, month - 1, day).getTime();
}

function showTooltip(x, y, label, data) {
	$('<div id="flot-tooltip">' + '<b>' + label + ': </b><i>' + data + '</i>' + '</div>').css({
		top: y + 5,
		left: x + 20
	}).appendTo("body").fadeIn(200);
}


function showtab() {
	return {
		link: function (scope, element, attrs) {
			element.click(function (e) {
				e.preventDefault();
				$(element).tab('show');
			});
		}
	};
}

function profileDropdown() {
	return {
		restrict: 'C',
		templateUrl: 'templates/common/userbox.html',
		controller: ['$scope', '$http', '$localStorage', '$location', 'toaster', '$rootScope', function ($scope, $http, $localStorage, $location, toaster, $rootScope) {
			$scope.userData = $localStorage.userData;
			$rootScope.$watch(function () {
				return localStorage.getItem('ngStorage-userData');
			}, function (newVal, oldVal) {
				if (newVal) {
					$scope.userData = $localStorage.userData;
				}
			
			})
			$scope.logoutBtn = function () {
				$http.get(baseUrl + "logout")
					.success(function (response) {
						if (response.status == 'success') {
							toaster.pop('success', response.result.msg);
							$localStorage.userData = {};
							setTimeout(function () {
								window.location = siteUrl + "login.html";
							}, 100);
						} else {
							toaster.pop('success', msg["0000"]);
						}
					}).error(function () {
						toaster.pop('success', msg["0000"]);
					});
			}

		}],
	};
}

function profile2Dropdown() {
	return {
		restrict: 'C',
		templateUrl: 'templates/common/userbox2.html',
		controller: ['$scope', '$http', '$localStorage', '$location', 'toaster', function ($scope, $http, $localStorage, $location, toaster) {
			$scope.userData = $localStorage.userData;
		}],
	};
}

app.directive('myTag', ['$http', function($http) {
return {
    restrict: 'E',
    transclude: true,
    replace: true,    
  template:'<input ng-model="search" placeholder="Search" ng-change="getSearchData()" type="text" class="form-control">',	
    scope:{
        src:"=" ,
		callback:"&"
    },
    controller:function($scope){
        //console.info("enter directive controller");
        
    $scope.getSearchData = function()
	{
		var str='';
			if($scope.search){
			str = '?search_keyword='+$scope.search;
			}else{
			str = "";
			}
			$http.get(baseUrl+$scope.src+ str ).success(function(response) {
		
				$scope.list = response.data;
				$scope.callback(response);
				$scope.totalItems = $scope.list.length;
			});


		
		//console.log($scope.src);
     }   
    /*     $http({method: 'GET', url:$scope.src}).then(function (result) {
                           console.log(result);                              
                        }, function (result) {
                            alert("Error: No data returned");
                        }); */
    }
}
}]);


// app.directive('addNew', [function() {
//     return {
//         restrict: 'E',
//         replace: true,
//         scope: {
//             route: '=',
//             path: '='
//         },

//         template: '<a data-match-route="/{{route}}" href="#{{path}}" class="btn btn-primary pull-right"><i class="fa fa-plus-circle fa-lg"></i> Add New</a>',

//     };
// }]);

function fileModel($parse) {
	return {
		restrict: 'A',
		link: function (scope, element, attrs) {
			var model = $parse(attrs.fileModel);
			var modelSetter = model.assign;

			element.bind('change', function () {
				scope.$apply(function () {
					modelSetter(scope, element[0].files[0]);
				});
			});
		}
	};
}

function authentication($parse) {
	return {
		restrict: 'E',
		controller: ['$scope', '$http', '$localStorage', '$location', 'toaster', function ($scope, $http, $localStorage, $location, toaster) {
			$http.get(baseUrl + "isLoggedIn")
				.success(function (response) {
					if (response.status == 'success') {
						$localStorage.userData = response.result.user_details;
						////console.log($localStorage.userData);
					} else {

						toaster.pop('error', response.result.msg);
						// setTimeout(function(){  
						window.location = siteUrl + "login.html";
						//}, 100); 
					}
				}).error(function () {
					toaster.pop('error', msg["0000"]);
				});
		}],
	};
}

function arrayPushService() {
	this.arrayPush = function (response_data, parent_array) {
		try {
			if (response_data.length > 0) {
				for (cnt_i in response_data) {
					parent_array.push(response_data[cnt_i]);

				}
				return parent_array;
			}
			else {
				return parent_array;
			}
		}
		catch (e) {
			////console.log(e);
		}
	}

}

angular
	.module('cubeWebApp')
	.directive('bsNavbar', bsNavbar)
	.directive('showtab', showtab)
	.directive('profileDropdown', profileDropdown)
	.directive('profile2Dropdown', profile2Dropdown)
	.directive('fileModel', fileModel)
	.directive('authentication', authentication)
	.directive('back', back)
	.service('arrayPushService', arrayPushService)
