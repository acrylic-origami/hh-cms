import React from 'react';
import {Matrix} from './GLSylvester';
import {makePerspective} from './Vendor/glUtils';

export type TCanvasControllerProps = {
	width: ?number,
	height: ?number,
	size_parent: ?HTMLElement,
};
export type TCanvasControllerState = {
	active: bool,
	hover: bool,
	width: number,
	height: number,
	projection: ?Matrix,
	screen: ?Matrix
};
const FOVY = 45;
export default class extends React.Component {
	props: TCanvasControllerProps;
	state: TCanvasControllerState;
	
	canvas: HTMLCanvasElement;
	ctx: RenderingContext;
	constructor(props: TCanvasControllerProps) {
		super(props);
		this.state = {
			active: true,
			hover: false,
			width: props.width,
			height: props.height,
			projection: null, // avoid singularities
			screen: null
		};
	}
	draw = () => {
		if(this.state.active)
			window.requestAnimationFrame(this.draw);
		
		this._draw();
	};
	_draw(): void {} // abstract
	_canvas_click_handler = () => {
		this.setState({
			active: true // !state_.active; instead, use [esc] to pause
		});
	};
	_canvas_key_handler = (e) => {
		if(e.keyCode === 27) // escape
			this.setState({
				active: false
			});
	}
	_canvas_hover_handler = (hover: bool) => {
		this.setState({ hover });
	};
	_resize_canvas = () => {
		const width = (this.props.width || (this.props.size_parent ? this.props.size_parent.offsetWidth : window.innerWidth)) * window.devicePixelRatio;
		const height = (this.props.height || (this.props.size_parent ? this.props.size_parent.offsetHeight : window.innerHeight)) * window.devicePixelRatio;
		this.setState({
			width,
			height,
			projection: height !== 0 ? makePerspective(FOVY, width / height, 1, 100) : null,
			screen: $M([
				[-width, 0, 0, width / 2],
				[0, -height, 0, height / 2],
				[0, 0, 1, 0],
				[0, 0, 0, 1]
			])
		});
	};
	_reffer = (canvas_: HTMLCanvasElement) => {
		this.canvas = canvas_;
		this.ctx = canvas_.getContext('2d');
	};
	_componentDidMount(): void {} // abstract
	componentDidMount(): void {
		this._resize_canvas();
		window.addEventListener('resize', this._resize_canvas);
		window.addEventListener('keyup', this._canvas_key_handler);
		
		if(this.state.active)
			window.requestAnimationFrame(this.draw);
		
		this._componentDidMount();
	}
	_componentWillUpdate(nextProps, nextState): void {} // abstract
	componentWillUpdate(nextProps, nextState): void {
		// avoid infinite loop from state setting
		if(this.props.width !== nextProps.width || this.props.height !== nextProps.height || this.props.size_parent !== nextProps.size_parent)
			this._resize_canvas();
		
		if(!this.state.active && nextState.active)
			window.requestAnimationFrame(this.draw);
		
		this._componentWillUpdate(nextProps, nextState);
	}
	render() {
		return <div className={'canvas-container' + (this.state.active ? ' active' : '')}>
			<canvas ref={this._reffer}
			        onClick={this._canvas_click_handler}
			        width={this.state.width}
			        height={this.state.height}
			        onMouseEnter={() => this._canvas_hover_handler(true)}
			        onMouseLeave={() => this._canvas_hover_handler(false)}></canvas>
		</div>;
	}
}