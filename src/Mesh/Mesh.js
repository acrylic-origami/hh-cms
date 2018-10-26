import {Vector, Matrix} from 'sylvester';
import MeshPoint from './MeshPoint';

export default class {
	constructor(points: Array<MeshPoint>) {
		this.points = points.map(point => point.clone());
	}
	shard(P0: Vector, P_SCREEN: Vector) { // origin: Vector /* Vector3 */, SCALE: number = 10, stroke_style: ?string = null): GeometryCollection<CanvasRenderingContext2D> {
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
		const P0_HAT = P0.toUnitVector();
		const negative_room = P0.modulus() - P0.subtract(P_SCREEN).modulus(); // user distance to screen vs. user distance to origin, in real world units (avoid scaling with change of focal distance)
		const rand_range = [-Math.atan(negative_room), Math.PI];
		for(let i = 0; i < this.points.length; i++) {
			const point = this.points[i];
			while(point.adj.length > 0) {
				const target_adj_idx = Math.floor(Math.random() * point.adj.length);
				const target_idx = point.adj[target_adj_idx];
				const target = this.points[target_idx];
				
				target.adj.splice(target.adj.indexOf(i), 1);
				point.adj.splice(target_adj_idx, 1);
				
				const original_points = build_path(i).reverse().concat(build_path(target_idx));
				
				// Cauchy-distributed
				const path_offset_factor = Math.tan(Math.random() * (rand_range[1] - rand_range[0]) + rand_range[0]);
				// console.log(path_offset_factor)
				
				paths.push(original_points.map(point => {
					const P3 = $V([point.e(1), point.e(2), 0]); // point.coerce(3);
					const point_offset_factor = (Math.random() * 0.1 - 0.05) * (path_offset_factor + negative_room);
					const d_P3_P0_hat = P3.subtract(P0).toUnitVector();
					return P3.add(d_P3_P0_hat.x(
						(point_offset_factor + path_offset_factor) /
						Math.abs(P0_HAT.dot(d_P3_P0_hat))
					));
				}));
			}
		}
		return paths;
	}
}