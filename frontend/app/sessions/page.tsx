'use client'

import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import api from '@/lib/api'
import { removeToken, isAuthenticated } from '@/lib/auth'

interface Session {
  id: string
  langue: string
  date: string
  heure: string
  lieu: string
  places: number
}

export default function Sessions() {
  const router = useRouter()
  const [sessions, setSessions] = useState<Session[]>([])
  const [page, setPage] = useState(1)
  const [total, setTotal] = useState(0)
  const [loading, setLoading] = useState(true)
  const [user, setUser] = useState<any>(null)

  useEffect(() => {
    if (!isAuthenticated()) {
      router.push('/login')
      return
    }
    fetchProfile()
    fetchSessions()
  }, [page])

  const fetchProfile = async () => {
    try {
      const { data } = await api.get('/user/profile')
      setUser(data)
    } catch (err) {
      console.error(err)
    }
  }

  const fetchSessions = async () => {
    try {
      const { data } = await api.get(`/sessions?page=${page}&limit=9`)
      setSessions(data.sessions)
      setTotal(data.total)
    } catch (err) {
      console.error(err)
    } finally {
      setLoading(false)
    }
  }

  const handleBook = async (sessionId: string) => {
    try {
      await api.post('/bookings', { session_id: sessionId })
      alert('✅ Réservation réussie !')
      fetchSessions()
    } catch (err: any) {
      alert('❌ ' + (err.response?.data?.error || 'Erreur lors de la réservation'))
    }
  }

  const handleLogout = () => {
    removeToken()
    router.push('/login')
  }

  if (loading) {
    return (
      <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh' }}>
        <div style={{ fontSize: '18px', fontWeight: '600', color: '#124fa0' }}>Chargement...</div>
      </div>
    )
  }

  return (
    <div>
      <nav className="navbar">
        <div className="navbar-brand">ETS EMEA</div>
        <div className="navbar-links">
          <a href="/sessions">Sessions</a>
          <a href="/my-bookings">Mes réservations</a>
          <a href="/profile">Profil</a>
          <button onClick={handleLogout} className="btn btn-danger" style={{ padding: '10px 20px', fontSize: '13px' }}>
            Déconnexion
          </button>
        </div>
      </nav>

      <div className="container">
        <div className="card">
          <h1 style={{ fontSize: '24px' }}>Sessions disponibles</h1>
          <p style={{ color: '#64748b', fontSize: '13px' }}>Bienvenue {user?.nom}</p>
        </div>

        <div className="grid">
          {sessions.map((session) => (
            <div key={session.id} className="session-card">
              <h3 style={{ fontSize: '16px' }}>{session.langue}</h3>
              <div style={{ marginTop: '16px', marginBottom: '16px' }}>
                <p style={{ marginBottom: '8px', fontSize: '13px' }}>
                  <strong style={{ color: '#475569' }}>Date:</strong> {new Date(session.date).toLocaleDateString('fr-FR')}
                </p>
                <p style={{ marginBottom: '8px', fontSize: '13px' }}>
                  <strong style={{ color: '#475569' }}>Heure:</strong> {session.heure}
                </p>
                <p style={{ marginBottom: '8px', fontSize: '13px' }}>
                  <strong style={{ color: '#475569' }}>Lieu:</strong> {session.lieu}
                </p>
                <p style={{ marginTop: '12px' }}>
                  <span className={session.places > 5 ? 'badge badge-success' : 'badge badge-warning'}>
                    {session.places} places
                  </span>
                </p>
              </div>
              <button
                onClick={() => handleBook(session.id)}
                className="btn btn-primary"
                style={{ width: '100%', padding: '11px', fontSize: '13px' }}
                disabled={session.places === 0}
              >
                {session.places === 0 ? 'Complet' : 'Réserver'}
              </button>
            </div>
          ))}
        </div>

        <div style={{ 
          display: 'flex', 
          justifyContent: 'center', 
          gap: '12px', 
          marginTop: '40px',
          alignItems: 'center' 
        }}>
          <button
            onClick={() => setPage(page - 1)}
            disabled={page === 1}
            className="btn btn-secondary"
            style={{ padding: '10px 20px', fontSize: '13px' }}
          >
            ← Précédent
          </button>
          <span style={{ 
            padding: '10px 20px',
            fontWeight: '600', 
            color: '#124fa0',
            fontSize: '13px'
          }}>
            Page {page}
          </span>
          <button
            onClick={() => setPage(page + 1)}
            disabled={page * 9 >= total}
            className="btn btn-secondary"
            style={{ padding: '10px 20px', fontSize: '13px' }}
          >
            Suivant →
          </button>
        </div>
      </div>
    </div>
  )
}