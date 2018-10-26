import {logo, bvrd, simplex, line} from './Assets/Meshes';
import XY2M from './Util/XY2M';
import Q from 'q';
import {DEFAULT_STROKE_STYLE} from './Geometry.js';

const P_SCREEN = $V([0, 0, 4]);
const P0 = $V([0, 0, 15]);
const d_0_SCREEN = P_SCREEN.subtract(P0);

const paths = bvrd.shard(P0, P_SCREEN);
const pixel_ratio = window.devicePixelRatio || 1;

function on_load_resize() {
	const canvas = document.getElementById('hero_canvas');
	const rect = canvas.getBoundingClientRect();
	canvas.width = rect.width * pixel_ratio;
	canvas.height = rect.height * pixel_ratio;
}

const art_scale_from_width = w => Math.max(w, 0.3*w + 500)

window.addEventListener('load', () => {
	const canvas = document.getElementById('hero_canvas');
	const ctx = canvas.getContext('2d', { alpha: false });
	
	let resized = true;
	on_load_resize();
	window.addEventListener('resize', () => {
		on_load_resize();
		resized = true;
	});

	// state machine for interactivity
	let is_interactive = false;
	let target = $V([0, 0]); // yaw, pitch
	const ANGULAR_RANGE = $V([Math.PI, Math.PI])
	
	canvas.addEventListener('mouseenter', e => {
		is_interactive = true;
	});
	canvas.addEventListener('mouseleave', (e) => {
		is_interactive = false;
		target = $V([0, 0]);
	});
	canvas.addEventListener('mousemove', e => {
		is_interactive = true;
		const offset = canvas.getBoundingClientRect();
		target = $V([
			-((e.pageX - offset.left) / canvas.offsetWidth - 0.5) * 
				ANGULAR_RANGE.e(1),
			((e.pageY - offset.top) / canvas.offsetHeight - 0.5) * ANGULAR_RANGE.e(2)
		]);
	});
	
	// set the line style
	
	const GAIN = 0.14;
	const PX_PER_PT = 220 * pixel_ratio;
	const DELTA_THRESH = 0.0001; // steady-state
	
	({
 		current: $V([ (Math.random() - 0.5) * ANGULAR_RANGE.e(1), (Math.random() - 0.5) * ANGULAR_RANGE.e(2) ]),
 		draw: function(t) {
 			window.requestAnimationFrame(this.draw.bind(this));
			
 			// logo sharding
 			const delta = target.subtract(this.current);
 			if(delta.modulus() > DELTA_THRESH || resized) {
 				if(resized) {
 					for(const prop in DEFAULT_STROKE_STYLE) {
 						if(DEFAULT_STROKE_STYLE.hasOwnProperty(prop))
 							ctx[prop] = DEFAULT_STROKE_STYLE[prop];
 					}
 					resized = false;
 				}
 				ctx.save();
 				ctx.fillStyle = '#4d3d59';
 				ctx.fillRect(0, 0, canvas.width, canvas.height);
 				ctx.restore();
 				
 				this.current = this.current.add(delta.x(GAIN));
 				const R = XY2M(this.current.e(2), this.current.e(1));
 				// console.log(Math.sin(this.current.e(0)));
 				// console.log(R.inspect());
 				
 				for(const path of paths) {
 					ctx.beginPath();
 					let first_point = true;
 					for(const point of path) {
 						const P0_ray = R.x(point).subtract(P0);
 						const draw_point_P = P0_ray.x(d_0_SCREEN.modulus() / P0_ray.e(3));
 						const draw_point = draw_point_P.add(P0);
 						// console.log(point.inspect(), world_point.inspect());
 						
 						(() => {
 							if(first_point) {
 								first_point = false;
 								return ctx.moveTo.bind(ctx);
 							}
 							else
 								return ctx.lineTo.bind(ctx);
 						})()(draw_point.e(1) * PX_PER_PT + canvas.width / 2, -draw_point.e(2) * PX_PER_PT + canvas.height / 2);
 					}
 					ctx.stroke();
 				}
 			}
	 	}).draw(performance.now());
	 });
}); // new Polyline([$V([-0.5, -0.5, 0]), $V([0.5, -0.5, 0]), $V([0, 0.5, 0])])

// new GeometryCollection([new Polyline([$V([-0.5, -0.5, 0]), $V([0.5, -0.5, 0]), $V([0, 0.5, 0])])])