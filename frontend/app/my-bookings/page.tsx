'use client'

import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import api from '@/lib/api'
import { removeToken, isAuthenticated } from '@/lib/auth'

interface Booking {
  id: string
  createdAt: string
  session: {
    id: string
    langue: string
    date: string
    heure: string
    lieu: string
  }
}

export default function MyBookings() {
  const router = useRouter()
  const [bookings, setBookings] = useState<Booking[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    if (!isAuthenticated()) {
      router.push('/login')
      return
    }
    fetchBookings()
  }, [])

  const fetchBookings = async () => {
    try {
      const { data } = await api.get('/bookings')
      setBookings(data.bookings)
    } catch (err) {
      console.error(err)
    } finally {
      setLoading(false)
    }
  }

  const handleCancel = async (bookingId: string) => {
    if (!confirm('⚠️ Êtes-vous sûr de vouloir annuler cette réservation ?')) {
      return
    }

    try {
      await api.delete(`/bookings/${bookingId}`)
      alert('✅ Réservation annulée avec succès')
      fetchBookings()
    } catch (err: any) {
      alert('❌ ' + (err.response?.data?.error || "Erreur lors de l'annulation"))
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
          <h1 style={{ fontSize: '24px' }}>Mes Réservations</h1>
          <p style={{ color: '#64748b', fontSize: '13px' }}>Gérez vos réservations</p>
        </div>

        {bookings.length === 0 ? (
          <div className="card" style={{ textAlign: 'center', padding: '50px' }}>
            <div style={{ fontSize: '48px', marginBottom: '16px' }}>📅</div>
            <h3 style={{ color: '#64748b', fontWeight: '500', fontSize: '16px' }}>Aucune réservation</h3>
            <p style={{ color: '#94a3b8', marginTop: '8px', fontSize: '13px' }}>Réservez votre première session !</p>
            <button 
              onClick={() => router.push('/sessions')}
              className="btn btn-primary"
              style={{ marginTop: '20px', padding: '11px 24px', fontSize: '13px' }}
            >
              Voir les sessions
            </button>
          </div>
        ) : (
          <div className="card">
            <table className="table">
              <thead>
                <tr>
                  <th>Langue</th>
                  <th>Date</th>
                  <th>Heure</th>
                  <th>Lieu</th>
                  <th>Réservé le</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                {bookings.map((booking) => (
                  <tr key={booking.id}>
                    <td style={{ fontWeight: '600', color: '#124fa0', fontSize: '13px' }}>{booking.session.langue}</td>
                    <td style={{ fontSize: '13px' }}>{new Date(booking.session.date).toLocaleDateString('fr-FR')}</td>
                    <td style={{ fontSize: '13px' }}>{booking.session.heure}</td>
                    <td style={{ fontSize: '13px' }}>{booking.session.lieu}</td>
                    <td style={{ fontSize: '13px' }}>{new Date(booking.createdAt).toLocaleDateString('fr-FR')}</td>
                    <td>
                      <button
                        onClick={() => handleCancel(booking.id)}
                        className="btn btn-danger"
                        style={{ padding: '8px 14px', fontSize: '12px' }}
                      >
                        Annuler
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  )
}