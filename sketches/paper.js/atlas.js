project.view.zoom = .5;
var pmouse, pcenter, ppoint, ctr;
var instance = [];

var hitOptions = {
	segments: true,
	stroke: true,
	fill: true,
	tolerance: 5
};

var elements_list;

jQuery.get('elements_list_json.txt', function(data) {
	
	var icone = ['immateriel', 'volume', 'plan'];
	var icones_loaded_num = 0;
	icone.forEach(function(entry) {
		console.log(entry);
		project.importSVG('icones/'+entry+'2.svg', function(item) {
			icone[entry] = new Symbol(item);
			item.remove();
			icone_loaded();
		});
	});
	
	function icone_loaded() {
		icones_loaded_num++ ;
		if (icones_loaded_num >= icone.length){
			console.log(icone);
			elements_list = JSON.parse(data);
			console.log(elements_list);
			for (var i = 0, l = elements_list.length ; i < l ; i++) {
				var x = parseInt( elements_list[i]['x'] ) || 0;
				var y = parseInt( elements_list[i]['y'] ) || 0;
				var class_a = elements_list[i]['class_a'];
				
				instance[i] = icone[class_a].place( [x, y] );
				instance[i].scale(6);
				//instance.position = Point( x, y );
			}
			//select( 'volume' );
		}
	}

});
function select(class_a) {
	var selected = [];
	for( var i = 0; i < elements_list.length; i++ ) {
		if ( class_a == elements_list[i]["class_a"] ) {
			instance[i].scale(2);
		}
	}
}

//----------------------- ZOOM / PAN -------------------------

function traslladar(a, b) {
	var center = paper.project.view.center;
	var desX = (a.x - b.x);
	var desY = (a.y - b.y);

	var newCenter = [center.x + desX, center.y + desY];
	return newCenter;
}
function onMouseDown(event) {
	path_pan = null;
	var hitResult = project.hitTest(event.point, hitOptions);
	if (!hitResult) {
		path_pan = new Point(event.point);
		//path_pan.add(event.point);
	}
}

function onMouseDrag(event) {
	if (path_pan) {
		path_pan.add(event.point);
		var des = traslladar(event.downPoint, event.point);
		paper.project.view.center = des;
	}
}
$('body, html').mousewheel(function (event) {
	project.view.zoom = Math.max((project.view.zoom + event.deltaY * .01), .01);
});