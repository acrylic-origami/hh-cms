import React from 'react';
import ReactDOM from 'react-dom';
import Spinner from './Spinner';
import Preview2D from './Preview2D';
import {logo, bvrd} from './Assets/Meshes';
import Polyline from './Geometry/Polyline';
import GeometryCollection from './Geometry/GeometryCollection';

const Z_DISTANCE = -10;

const geom = bvrd.shard($V([0, 0, Z_DISTANCE]));
window.addEventListener('load', () =>
	ReactDOM.render(
		// <Preview2D geometry={logo_geom} />,
		<Spinner z_distance={Z_DISTANCE} geometry={geom} />,
		document.getElementById('main')
	)
); // new Polyline([$V([-0.5, -0.5, 0]), $V([0.5, -0.5, 0]), $V([0, 0.5, 0])])

// new GeometryCollection([new Polyline([$V([-0.5, -0.5, 0]), $V([0.5, -0.5, 0]), $V([0, 0.5, 0])])])