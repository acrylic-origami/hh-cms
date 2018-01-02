import {Vector, Matrix} from '../GLSylvester';
import MeshPoint from './MeshPoint';
import type {Path} from './Path';

import Polyline from './Geometry/Polyline';
import GeometryCollection from './Geometry/GeometryCollection';

export default class {
	constructor(points: Array<MeshPoint>) {
		this.points = points.map(point => point.clone());
	}
	shard(SCALE: number = 1, stroke_style: ?string = null): GeometryCollection<CanvasRenderingContext2D> {
		stroke_style = stroke_style || DEFAULT_STROKE_STYLE;
		const build_path = (idx: number) => {
			const point = this.points[idx];
			
			if(point.adj.length > 0 && Math.random() > 1 / (point.adj.length + 1)) {
				const target = Math.floor(Math.random() * point.adj.length);
				this.points[target].adj.splice(this.points[target].adj.indexOf(idx), 1);
				point.adj.splice(target, 1);
				return [point.coords].concat(build_path(target));
			}
			else
				return [point.coords];
		}
		
		const paths = [];
		for(let i = 0; i < this.points.length; i++) {
			const point = this.points[i];
			while(point.adj.length > 0) {
				const target = Math.floor(Math.random() * point.adj.length);
				point.adj.splice(target, 1);
				
				paths.push(new Polyline(
					build_path(i).reverse().concat([point.coords]).concat(build_path(target))
				));
			}
		}
		return new GeometryCollection(paths);
	}
}