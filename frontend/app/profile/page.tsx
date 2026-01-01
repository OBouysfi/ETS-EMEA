'use client'

import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import api from '@/lib/api'
import { removeToken, isAuthenticated } from '@/lib/auth'

export default function Profile() {
  const router = useRouter()
  const [formData, setFormData] = useState({
    nom: '',
    email: '',
  })
  const [loading, setLoading] = useState(true)
  const [success, setSuccess] = useState('')
  const [error, setError] = useState('')

  useEffect(() => {
    if (!isAuthenticated()) {
      router.push('/login')
      return
    }
    fetchProfile()
  }, [])

  const fetchProfile = async () => {
    try {
      const { data } = await api.get('/user/profile')
      setFormData({ nom: data.nom, email: data.email })
    } catch (err) {
      console.error(err)
    } finally {
      setLoading(false)
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError('')
    setSuccess('')

    try {
      await api.put('/user/profile', formData)
      setSuccess('Profil mis à jour avec succès')
    } catch (err: any) {
      setError(err.response?.data?.error || 'Erreur lors de la mise à jour')
    }
  }

  const handleLogout = () => {
    removeToken()
    router.push('/login')
  }

  if (loading) {
    return <div className="container">Chargement...</div>
  }

  return (
    <div>
      <nav className="navbar">
        <div className="navbar-brand">ETS EMEA</div>
        <div className="navbar-links">
          <a href="/sessions">Sessions</a>
          <a href="/my-bookings">Mes réservations</a>
          <a href="/profile">Profil</a>
          <button onClick={handleLogout} className="btn btn-secondary" style={{ padding: '8px 16px' }}>
            Déconnexion
          </button>
        </div>
      </nav>

      <div className="container">
        <div className="card" style={{ maxWidth: '600px', margin: '0 auto' }}>
          <h1 style={{ color: '#667eea', marginBottom: '30px' }}>Mon Profil</h1>

          {success && <div className="alert alert-success">{success}</div>}
          {error && <div className="alert alert-error">{error}</div>}

          <form onSubmit={handleSubmit}>
            <label style={{ display: 'block', marginBottom: '8px', fontWeight: '600', color: '#374151' }}>
              Nom complet
            </label>
            <input
              type="text"
              className="input"
              value={formData.nom}
              onChange={(e) => setFormData({ ...formData, nom: e.target.value })}
              required
            />

            <label style={{ display: 'block', marginBottom: '8px', fontWeight: '600', color: '#374151' }}>
              Email
            </label>
            <input
              type="email"
              className="input"
              value={formData.email}
              onChange={(e) => setFormData({ ...formData, email: e.target.value })}
              required
            />

            <button type="submit" className="btn btn-primary" style={{ width: '100%', marginTop: '20px' }}>
              Mettre à jour
            </button>
          </form>
        </div>
      </div>
    </div>
  )
}