import React from 'react';
import {Vector, Matrix} from './GLSylvester';
import {makeLookAt} from './Vendor/glUtils';

import Mesh from './Mesh/Mesh';
import type {TCanvasControllerProps, TCanvasControllerState} from './CanvasController';
import CanvasController from './CanvasController';
import type {Path} from './Mesh/Path';
import {makePerspective} from './Vendor/glUtils';

type TProps = TCanvasControllerProps & {
	geometry: Geometry,
	z_distance: number,
	angular_range: Vector
};
type TState = TCanvasControllerState & {
	view: Matrix
};
export default class extends CanvasController {
	props: TProps;
	origin: Vector;
	static defaultProps = {
		z_distance: 10,
		angular_range: $V([ Math.PI, Math.PI / 2 ]) // yaw, pitch
	};
	constructor(props: TProps) {
		super(props);
		this.origin = this.props.geometry.centroid();
		console.log(this.origin);
		
		this.state.view = null;
	}
	_canvas_move_handler = (e) => {
		const yaw = ((e.pageX - this.canvas.offsetLeft) / this.canvas.offsetWidth - 0.5) * this.props.angular_range.e(1);
		const pitch = ((e.pageY - this.canvas.offsetTop) / this.canvas.offsetHeight - 0.5) * this.props.angular_range.e(2);
		
		this.setState({
			view: makeLookAt(
				$V([
					Math.sin(yaw) * Math.cos(pitch),
					Math.sin(pitch),
					Math.cos(yaw) * Math.cos(pitch)
				]).x(this.props.z_distance),
				this.origin,
				$V([
					Math.sin(yaw) * Math.sin(pitch),
					Math.cos(pitch),
					Math.cos(yaw) * Math.sin(pitch)
				])
			)
		});
	};
	_componentDidMount() {
		this.canvas.addEventListener('mousemove', this._canvas_move_handler);
	}
	_draw(): void {
		this.ctx.clearRect(0, 0, this.state.width, this.state.height);
		if(this.state.projection != null && this.state.screen != null && this.state.view != null)
			this.props.geometry.draw(this.ctx, this.state.screen.x(this.state.projection).x(this.state.view));
	}
}