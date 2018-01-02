import {Vector, Matrix} from '../GLSylvester';
import {Geometry} from '../Geometry';

export default class<TContext : RenderingContext> { // implements Geometry<TContext>
	geometries: Array<Geometry<TContext>>;
	dimensions: number;
	local_transform: Matrix;
	constructor(geometries: Array<Geometry<TContext>>, local_transform: Matrix = null, dimensions: number = 3) {
		this.geometries = geometries;
		this.dimensions = dimensions;
		this.local_transform = local_transform || Matrix.I(dimensions + 1);
	}
	centroid(): Vector {
		return this.local_transform.x(
			this.geometries.reduce((acc, geom) => acc.add(geom.centroid().x(geom.mass()).coerce(this.dimensions + 1)), Vector.Zero(this.dimensions + 1))
		).x(1 / this.mass()).coerce(this.dimensions);
	}
	mass(): number {
		return this.geometries.reduce((acc, geom) => acc + geom.mass(), 0);
	}
	draw(ctx: TContext, projection: Matrix): void {
		for(let geometry of this.geometries)
			geometry.draw(ctx, projection);
		// this.geometries[0].draw(ctx, projection);
	}
	set_transform(transform: Matrix): void {
		this.local_transform = transform;
	}
}