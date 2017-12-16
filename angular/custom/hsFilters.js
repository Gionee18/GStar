app.filter('statusFilter', function() {
	return function(input) {
		
		for(var i=0;i < CatStatus.length;i++){
			if( CatStatus[i]['value'] == Number(input) ){
				return CatStatus[i]['name'];
			}
		}
	};
});

app.filter('approvalFilter', function() {
	return function(input) {
		
		for(var i=0;i < ApprovalStatus.length;i++){
			if( ApprovalStatus[i]['value'] == Number(input) ){
				return ApprovalStatus[i]['name'];
			}
		}
	};
});


app.filter('docsIcons', function() {
	return function(input) {
		for(var i=0;i<DocsIcons.length;i++ ){
			if( DocsIcons[i]['type'] == input ){
				return DocsIcons[i]['icon'];
			}
		}
		if(input == "mp4" || input == "MP4" || input == "3gp" || input == "3GP"){
			return siteUrl+'assets/img/video.jpg';
		}
	};
});

app.filter('statusClass', function() {
	return function(input) {
		for(var i=0;i < CatStatus.length;i++){
			if( CatStatus[i]['value'] == Number(input) ){
				return CatStatus[i].cssClass;
			}
		}
	};
});

app.filter('approvedstatusClass', function() {
	return function(input) {
		for(var i=0;i < ApprovalStatus.length;i++){
			if( ApprovalStatus[i]['value'] == Number(input) ){
				return ApprovalStatus[i].cssClass;
			}
		}
	};
});

app.filter('userRole', function() {
	return function(input) {
		for(var i=0;i < Roles.length;i++){
			if( Roles[i]['value'] == Number(input) ){
				return Roles[i].name;
			}
		}
	};
});