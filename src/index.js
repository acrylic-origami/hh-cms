import {logo, bvrd, simplex, line} from './Assets/Meshes';
import XY2M from './Util/XY2M';
import Q from 'q';
import {DEFAULT_STROKE_STYLE} from './Geometry.js';

const P_SCREEN = $V([0, 0, 4]);
const P0 = $V([0, 0, 15]);
const d_0_SCREEN = P_SCREEN.subtract(P0);

const paths = logo.shard(P0, P_SCREEN);
const pixel_ratio = window.devicePixelRatio || 1;

function on_load_resize_dirty_flag() {
	const canvas = document.getElementById('hero_canvas');
	const rect = canvas.getBoundingClientRect();
	canvas.width = rect.width * pixel_ratio;
	canvas.height = rect.height * pixel_ratio;
}

const art_scale_from_width = w => Math.max(w, 0.3*w + 500)

window.addEventListener('load', () => {
	const canvas = document.getElementById('hero_canvas');
	const ctx = canvas.getContext('2d', { alpha: false });
	
	let resize_dirty_flag = true;
	on_load_resize_dirty_flag();
	window.addEventListener('resize_dirty_flag', () => {
		on_load_resize_dirty_flag();
		resize_dirty_flag = true;
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
	const PX_PER_PT = 30 * pixel_ratio;
	const DELTA_THRESH = 0.0001; // steady-state
	
	const F_bg = Q.all(['img/graphics/pattern-0.25.png', 'img/graphics/pattern-b.png'].map(src => {
		const e = new Image();
		const D = Q.defer();
		e.src = src;
		e.onload = () => {
			const temp_canvas = document.createElement('canvas'),
			      temp_ctx = temp_canvas.getContext('2d');
			
			temp_canvas.width = 12 * pixel_ratio;
			temp_canvas.height = 12 * pixel_ratio;
			temp_ctx.drawImage(e, 0, 0, temp_canvas.width, temp_canvas.height);
			
			D.resolve(temp_canvas);
		};
		return D.promise;
	}));
	const F_fins = Q.all([1, 2, 3, 4].map(idx => {
		const e = new Image();
		const D = Q.defer();
		e.src = `img/graphics/fins/${idx}.png`;
		e.onload = () => D.resolve(e);
		return D.promise;
	}));
	
	Q.all([F_bg, F_fins]).then(([[ h_pattern, h_pattern_b ], h_fins]) => {
		const make_closed_poly = (ctx, waypoints) => {
			ctx.beginPath();
			ctx.moveTo(waypoints[0][0], waypoints[0][1]);
			for(const waypoint of waypoints.slice(1))
				ctx.lineTo(waypoint[0], waypoint[1]);
			ctx.closePath();
		};
		
		const bounding_rect = (waypoints) => {
			const full = [
				(a, b) => a[0] < b[0],
				(a, b) => a[1] < b[1],
				(a, b) => a[0] > b[0],
				(a, b) => a[1] > b[1]
			].map(f => waypoints.reduce((a, b) => f(a, b) ? a : b));
			return [full[0][0], full[1][1], full[2][0], full[3][1]];
		}
		({
	 		current: $V([ (Math.random() - 0.5) * ANGULAR_RANGE.e(1), (Math.random() - 0.5) * ANGULAR_RANGE.e(2) ]),
	 		draw: function(t) {
	 			window.requestAnimationFrame(this.draw.bind(this));
					
	 			const SCALE = art_scale_from_width(canvas.getBoundingClientRect().width) * pixel_ratio; // [0, 1] -> true pixels
	 			const SAFETY_MARGIN = 0.02;
	 			
	 			const fin_ground = [467/1280, 319/1280]; // width-relative pixels
	 			for(const h_fin of h_fins) {
	 				const aspect_ratio = h_fin.width / h_fin.height;
	 				const true_height = (1 + SAFETY_MARGIN) * fin_ground[1] * SCALE;
	 				ctx.drawImage(h_fin, fin_ground[0] * SCALE, fin_ground[1] * SCALE - true_height, true_height * aspect_ratio, true_height);
	 			}
	 			
	 			
	 			if(resize_dirty_flag) {
		 			ctx.save();
		 			// CCW clipping masks
		 			(() => {
			 			const waypoints = [[0, 0], [0, 242.18 / 1280], [74.1164 / 1280, 285.9054 / 1280], [467.0227 / 1280, 319.434 / 1280], [852.72 / 1280, 0]]
			 				.map(w => [w[0] * SCALE, w[1] * SCALE]); // no safety margin for the corner that ducks behind the black stage for sharded logo
			 			const pattern = ctx.createPattern(h_pattern_b, 'repeat');
						
			 			make_closed_poly(ctx, waypoints);
			 			ctx.fillStyle = 'white';
			 			ctx.fill();
			 			
						make_closed_poly(ctx, waypoints);
			 			ctx.fillStyle = pattern;
			 			ctx.fill();
		 			})();
		 			ctx.restore();
	 			}
	 			
	 			
	 			const delta = target.subtract(this.current);
	 			if(delta.modulus() > DELTA_THRESH || resize_dirty_flag) {
		 			ctx.save();
		 			(() => {
			 			const waypoints = [[74.1164 / 1280, 285.9054 / 1280], [74.1164 / 1280, 220.52 / 1280], [241.813 / 1280, 123.0864 / 1280], [467.0227 / 1280, 254.57 / 1280], [467.0227 / 1280, 319.434 / 1280], [299.3568 / 1280, 416.87 / 1280]]
			 				.map(w => [w[0] * SCALE, w[1] * SCALE]); // no safety margin for the corner that ducks behind the black stage for sharded logo
			 			const bound = bounding_rect(waypoints);
			 			const pattern = ctx.createPattern(h_pattern, 'repeat');
			 			
						make_closed_poly(ctx, waypoints);
			 			ctx.clip();
			 			
			 			ctx.fillStyle = 'black';
			 			ctx.fillRect(bound[0], bound[1], bound[2] - bound[0], bound[3] - bound[1]);
			 			ctx.fillStyle = pattern;
			 			ctx.fillRect(bound[0], bound[1], bound[2] - bound[0], bound[3] - bound[1]);
			 			
			 			// logo sharding
			 			(() => {
		 					for(const prop in DEFAULT_STROKE_STYLE) {
		 						if(DEFAULT_STROKE_STYLE.hasOwnProperty(prop))
		 							ctx[prop] = DEFAULT_STROKE_STYLE[prop];
		 					}
		 				
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
			 						})()(draw_point.e(1) * PX_PER_PT + (bound[0] + bound[2]) / 2, -draw_point.e(2) * PX_PER_PT + (bound[1] + bound[3]) / 2);
			 					}
			 					ctx.stroke();
				 			}
				 		})();
		 			})();
		 			ctx.restore();
	 			}
		 		
		 		resize_dirty_flag = false;
	 		}
	 	}).draw(performance.now());
	 });
}); // new Polyline([$V([-0.5, -0.5, 0]), $V([0.5, -0.5, 0]), $V([0, 0.5, 0])])

// new GeometryCollection([new Polyline([$V([-0.5, -0.5, 0]), $V([0.5, -0.5, 0]), $V([0, 0.5, 0])])])