import React from 'react'
import {PropTypes as T} from 'prop-types'
import Dropdown from 'react-bootstrap/lib/Dropdown'

const MenuOverlay = props =>
  <Dropdown
    id={props.id}
    pullRight={'right' === props.align}
    dropup={'top' === props.position}
    className={props.className}
    disabled={props.disabled}
  >
    {props.children}
  </Dropdown>

MenuOverlay.propTypes = {
  id: T.string.isRequired,
  className: T.string,
  disabled: T.bool,
  position: T.oneOf(['top', 'bottom']),
  align: T.oneOf(['left', 'right']),
  children: T.node.isRequired
}

MenuOverlay.defaultProps = {
  disabled: false,
  position: 'bottom',
  align: 'left'
}

export {
  MenuOverlay
}
