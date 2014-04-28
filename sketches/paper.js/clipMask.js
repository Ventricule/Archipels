project.currentStyle = {
	fillColor: 'black'
};

var ballPositions = [[255, 129], [610, 73], [486, 363],
	[117, 459], [484, 726], [843, 306], [789, 615], [1049, 82],
	[1292, 428], [1117, 733], [1352, 86], [92, 798]];



var circlePaths = [];
var radius = 50;
for (var i = 0, l = ballPositions.length; i < l; i++) {
	var circlePath = new Path.Circle({
		center: ballPositions[i],
		radius: 50
	});
	
	var raster = new Raster('http://st.depositphotos.com/1001559/2137/v/950/depositphotos_21378249-Seamless-geometric-pattern-in-fish-scale-design..jpg');
	raster.position = ballPositions[i];
	
	// Mask the image:
	var group = new Group({
		children: [circlePath, raster],
		clipped: true
	});
	
	circlePaths.push(circlePath);
}