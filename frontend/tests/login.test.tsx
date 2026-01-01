import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import Login from '@/app/login/page'
import api from '@/lib/api'

jest.mock('@/lib/api')
jest.mock('next/navigation', () => ({
  useRouter: () => ({
    push: jest.fn(),
  }),
}))

describe('Login Page', () => {
  it('renders login form', () => {
    render(<Login />)
    expect(screen.getByPlaceholderText('Email')).toBeInTheDocument()
    expect(screen.getByPlaceholderText('Mot de passe')).toBeInTheDocument()
  })

  it('switches to register mode', () => {
    render(<Login />)
    const toggleButton = screen.getByText("Pas de compte ? S'inscrire")
    fireEvent.click(toggleButton)
    expect(screen.getByPlaceholderText('Nom complet')).toBeInTheDocument()
  })

  it('submits login form', async () => {
    const mockPost = api.post as jest.Mock
    mockPost.mockResolvedValue({ data: { token: 'test-token' } })

    render(<Login />)
    
    fireEvent.change(screen.getByPlaceholderText('Email'), {
      target: { value: 'test@example.com' },
    })
    fireEvent.change(screen.getByPlaceholderText('Mot de passe'), {
      target: { value: 'password123' },
    })

    fireEvent.click(screen.getByText('Se connecter'))

    await waitFor(() => {
      expect(mockPost).toHaveBeenCalledWith('/auth/login', {
        email: 'test@example.com',
        password: 'password123',
      })
    })
  })
})