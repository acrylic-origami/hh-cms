import {Vector, Matrix} from '../GLSylvester';
import {Geometry, DEFAULT_STROKE_STYLE} from '../Geometry';

export default class { // implements Geometry<CanvasRenderingContext2D>
	points: Array<Vector>;
	constructor(points: Array<Vector> /* Vector3 */, local_transform: Matrix = null /* Matrix4 */, stroke_style: ?string = null) {
		this.points = points.map(point => point.coerce(4).add($V([0, 0, 0, 1]))); // usually Vector3 -> Vector4
		this.local_transform = (local_transform || Matrix.I(4)).ensure4x4(); // or maybe throw err for non-4x4
		this.stroke_style = stroke_style || DEFAULT_STROKE_STYLE;
	}
	centroid(): Vector { // Vector3
		return this.local_transform.x(
			this.points.reduce((acc, point) => acc.add(point), Vector.Zero(4))
		              .x(1 / this.mass())
		).coerce(3);
	}
	mass(): number {
		return this.points.length;
	}
	draw(ctx: CanvasRenderingContext2D, projection: ?Matrix = null): void { // bounding ctx ruins argument variance but (shrug emoji)
		projection = projection || Matrix.I(4);
		
		for(const prop in DEFAULT_STROKE_STYLE) {
			if(DEFAULT_STROKE_STYLE.hasOwnProperty(prop))
				ctx[prop] = DEFAULT_STROKE_STYLE[prop];
		}
		ctx.beginPath();
		
		let denorm_screen_point = projection.x(this.local_transform) // model -> projection product
			                                 .x(this.points[0]);
		ctx.moveTo.apply(ctx, denorm_screen_point.x(1 / denorm_screen_point.e(4)) // normalize by w
			                                      .coerce(2)
			                                      .flatten()
			             );
		
		for(let i = 1; i < this.points.length; i++) {
			const point = this.points[i];
			denorm_screen_point = projection.x(this.local_transform) // model -> projection product
				                             .x(point); // denormed screen space
			ctx.lineTo.apply(ctx, denorm_screen_point.x(1 / denorm_screen_point.e(4)) // normalize by w
				                                      .coerce(2)
				                                      .flatten()
			                );
		}
		// debugger;
		ctx.stroke();
	}
	set_transform(transform: Matrix): void {
		this.local_transform = transform;
	}
}