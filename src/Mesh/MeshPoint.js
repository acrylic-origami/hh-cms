import {Vector, Matrix} from 'sylvester';
import {Map, Set} from 'immutable';

export default class MeshPoint {
	constructor(coords: Array<Vector>, adj: Array<number>) {
		this.coords = coords;
		this.adj = adj;
	}
	clone(): MeshPoint {
		return new MeshPoint(this.coords.dup(), this.adj.slice(0));
	}
}