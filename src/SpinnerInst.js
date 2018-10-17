import React from 'react';
import Spinner from './Spinner';
import type {TSpinnerProps, TSpinnerState} from './Spinner';

type TState = TSpinnerState & {
	wrapper: HTMLElement
};
export default class extends React.Component {
	state: TState;
	constructor(props: TSpinnerProps) {
		super(props);
		this.state = {
			wrapper: null
		};
	}
	_reffer = () => {
		this.setState(state_ => (state_.wrapper ? {} : { wrapper: div_ }));
	}
	render() {
		const props = Object.assign({}, this.props, {
			size_parent: this.state.wrapper
		});
		return <div id="canvas-wrapper" ref={div_ => this._reffer}>
			<Spinner {...props}  />
		</div>;
	}
}