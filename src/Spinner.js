import React from 'react';
import {Vector, Matrix} from './GLSylvester';
import {makeLookAt, makePerspective} from './Vendor/glUtils';

import Mesh from './Mesh/Mesh';
import type {TCanvasControllerProps, TCanvasControllerState} from './CanvasController';
import CanvasController from './CanvasController';
import type {Path} from './Mesh/Path';

export type TSpinnerProps = TCanvasControllerProps & {
	geometry: Geometry,
	z_distance: number,
	angular_range: Vector
};
export type TSpinnerState = TCanvasControllerState & {
	current: Vector,
	target: Vector
};
export default class extends CanvasController {
	props: TSpinnerProps;
	origin: Vector;
	static defaultProps = {
		z_distance: 10,
		angular_range: $V([ Math.PI, Math.PI / 2 ]) // yaw, pitch
	};
	constructor(props: TSpinnerProps) {
		super(props);
		this.origin = props.geometry.centroid();
		
		this.state.current = $V([0, 0]);
		this.state.target = $V([0, 0]);
	}
	_canvas_move_handler = (e) => {
		const offset = this.canvas.getBoundingClientRect();
		const yaw = ((e.pageX - offset.left) / this.canvas.offsetWidth - 0.5) * this.props.angular_range.e(1);
		const pitch = -((e.pageY - offset.top) / this.canvas.offsetHeight - 0.5) * this.props.angular_range.e(2);
		
		this.setState({
			target: $V([yaw, pitch])
		});
	};
	_componentWillUpdate(nextProps, nextState) {
		if(!nextState.hover && this.state.hover !== nextState.hover)
			this.setState({
				target: $V([0, 0])
			});
	}
	_componentDidMount() {
		this.canvas.addEventListener('mousemove', this._canvas_move_handler);
	}
	_draw(): void {
		const view = makeLookAt(
			$V([
				Math.sin(this.state.current.e(1)) * Math.cos(this.state.current.e(2)),
				Math.sin(this.state.current.e(2)),
				Math.cos(this.state.current.e(1)) * Math.cos(this.state.current.e(2))
			]).x(this.props.z_distance),
			this.origin,
			$V([
				Math.sin(this.state.current.e(1)) * Math.sin(this.state.current.e(2)),
				Math.cos(this.state.current.e(2)),
				Math.cos(this.state.current.e(1)) * Math.sin(this.state.current.e(2))
			])
		);
		
		this.ctx.clearRect(0, 0, this.state.width, this.state.height);
		if(this.state.projection != null && this.state.screen != null)
			this.props.geometry.draw(this.ctx, this.state.screen.x(this.state.projection).x(view));
		
		this.setState(state_ => ({
			current: state_.current.add(state_.target.subtract(state_.current).x(0.03))
		}));
	}
}