var original_positions = [], transformed_position = [], points = new Group(), zoom = 1 ;
jQuery.get('elements_list_json.txt', function (data) {
	elements_list = JSON.parse(data);
	for (var i = 0, l = elements_list.length; i < l; i++) {
		var x = parseInt(elements_list[i]['x']) || 0;
		var y = parseInt(elements_list[i]['y']) || 0;
		

		original_positions.push( [x, y] );
		transformed_position.push( [x, y] );
	}
	console.log(original_positions);
});
$('body, html').mousewheel(function (event) {
	for (var i = 0; i < original_positions.length; i++) {
		zoom += event.deltaY * .0025;
		transformed_position[i][0] = original_positions[i][0] * zoom ;
		transformed_position[i][1] = original_positions[i][1] * zoom ;
		console.log(transformed_position[i][0]);
		
		points.removeChildren()
		point = new Shape.Circle({
			center: [transformed_position[i][0], transformed_position[i][1]],
			radius: 5,
			fillColor: 'black'
		});
		points.addChild(point);
	}
});