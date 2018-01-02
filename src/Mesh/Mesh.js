import {Vector, Matrix} from '../GLSylvester';
import MeshPoint from './MeshPoint';
import type {Path} from './Path';

import Polyline from '../Geometry/Polyline';
import GeometryCollection from '../Geometry/GeometryCollection';

export default class {
	constructor(points: Array<MeshPoint>) {
		this.points = points.map(point => point.clone());
	}
	shard(origin: Vector /* Vector3 */, SCALE: number = 10, stroke_style: ?string = null): GeometryCollection<CanvasRenderingContext2D> {
		const build_path = (idx: number): Array<Vector> => {
			const point = this.points[idx];
			
			if(point.adj.length > 0 && Math.random() > 1 / (point.adj.length + 1)) {
				const target_adj_idx = Math.floor(Math.random() * point.adj.length);
				const target_idx = point.adj[target_adj_idx];
				const target = this.points[target_idx];
				
				target.adj.splice(target.adj.indexOf(idx), 1);
				point.adj.splice(target_adj_idx, 1);
				
				return [point.coords].concat(build_path(target_idx));
			}
			else
				return [point.coords];
		}
		
		const paths = [];
		for(let i = 0; i < this.points.length; i++) {
			const point = this.points[i];
			while(point.adj.length > 0) {
				const target_adj_idx = Math.floor(Math.random() * point.adj.length);
				const target_idx = point.adj[target_adj_idx];
				const target = this.points[target_idx];
				
				target.adj.splice(target.adj.indexOf(i), 1);
				point.adj.splice(target_adj_idx, 1);
				
				const original_points = build_path(i).reverse().concat(build_path(target_idx));
				
				const path_offset_factor = Math.pow(1.5, (Math.random() * 2 - 1) * SCALE);
				
				paths.push(new Polyline(original_points.map(point => {
					const point_offset_factor = Math.pow(1.5, (Math.random() * 2 - 1) * SCALE / 10);
					const point_offset = point.coerce(3).subtract(origin);
					return point.coerce(3).add(point_offset.x(path_offset_factor + point_offset_factor));
				})));
			}
		}
		return new GeometryCollection(paths);
	}
}