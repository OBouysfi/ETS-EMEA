import { render, screen, waitFor } from '@testing-library/react'
import Sessions from '@/app/sessions/page'
import api from '@/lib/api'

jest.mock('@/lib/api')
jest.mock('next/navigation', () => ({
  useRouter: () => ({
    push: jest.fn(),
  }),
}))
jest.mock('@/lib/auth', () => ({
  isAuthenticated: () => true,
  removeToken: jest.fn(),
}))

describe('Sessions Page', () => {
  it('displays sessions list', async () => {
    const mockGet = api.get as jest.Mock
    mockGet.mockResolvedValueOnce({
      data: { nom: 'Test User', email: 'test@example.com' },
    })
    mockGet.mockResolvedValueOnce({
      data: {
        sessions: [
          {
            id: '1',
            langue: 'English',
            date: '2026-03-15',
            heure: '10:00',
            lieu: 'Paris',
            places: 10,
          },
        ],
        total: 1,
      },
    })

    render(<Sessions />)

    await waitFor(() => {
      expect(screen.getByText('English')).toBeInTheDocument()
      expect(screen.getByText('Paris')).toBeInTheDocument()
    })
  })
})