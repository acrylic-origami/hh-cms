import {Matrix} from 'sylvester';

const s = Math.sin;
const c = Math.cos;

export default (X, Y) => $M([
	[c(Y),  -s(Y)*s(X), s(Y)*c(X)],
	[0,     c(X),       s(X)     ],
	[-s(Y), -c(Y)*s(X), c(Y)*c(X)]
]);