import Mesh from './Mesh/Mesh';
import type {TCanvasControllerProps, TCanvasControllerState} from './CanvasController';
import CanvasController from './CanvasController';

type TProps = TCanvasControllerProps & {
	geometry: GeometryCollection
};
export default class extends CanvasController {
	constructor(props: TProps) {
		super(props);
	}
	_draw() {
		this.ctx.strokeStyle = '#000 solid 1px';
		for(const geometry of this.props.geometry.geometries) {
			this.ctx.beginPath();
			for(const point of geometry.points) {
				const moved_point = point.x(10).add($V([600, 350, 0, 0]));
				this.ctx.lineTo(moved_point.e(1), moved_point.e(2));
			}
			this.ctx.stroke();
		}
		this.setState({
			active: false
		});
	}
}