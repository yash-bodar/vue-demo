import { Notyf } from 'notyf'
import 'notyf/notyf.min.css'

// Create instance
const notyf = new Notyf({
  duration: 2500,
  position: { x: 'left', y: 'bottom' },
})

// Simple function to use anywhere
export function showToast(message, type = 'success') {
  notyf.open({
    type: type, // 'success' | 'error'
    message: message,
    ripple: true,
    dismissible: true,
  })
}
