import {Matrix, Vector} from './GLSylvester';

export interface Geometry<TContext: RenderingContext> {
	local_transform: Matrix;
	centroid(): Vector;
	mass(): number;
	draw(ctx: TContext, projection: Matrix): void;
}

export const DEFAULT_STROKE_STYLE = {
	strokeStyle: '#F2273B',
	lineWidth: 8,
	lineCap: 'butt',
	lineJoin: 'miter',
	miterLimit: 10.0
}