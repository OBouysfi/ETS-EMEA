'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import api from '@/lib/api'
import { setToken } from '@/lib/auth'

export default function Login() {
  const router = useRouter()
  const [isRegister, setIsRegister] = useState(false)
  const [formData, setFormData] = useState({
    nom: '',
    email: '',
    password: '',
  })
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError('')
    setLoading(true)

    try {
      if (isRegister) {
        await api.post('/auth/register', formData)
        setIsRegister(false)
        setFormData({ nom: '', email: '', password: '' })
        alert('✅ Inscription réussie ! Connectez-vous maintenant.')
      } else {
        const { data } = await api.post('/auth/login', {
          email: formData.email,
          password: formData.password,
        })
        setToken(data.token)
        router.push('/sessions')
      }
    } catch (err: any) {
      setError(err.response?.data?.message || err.response?.data?.error || 'Une erreur est survenue')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="auth-container">
      <div className="auth-card" style={{ maxWidth: '440px', padding: '40px' }}>

        <h1 className="auth-title" style={{ fontSize: '24px', marginBottom: '6px' }}>
          {isRegister ? 'Inscription' : 'ETS EMEA'}
        </h1>
        <p className="auth-subtitle" style={{ marginBottom: '28px', fontSize: '13px' }}>
          {isRegister 
            ? 'Créez votre compte pour continuer' 
            : 'Accédez à votre espace'}
        </p>

        {error && (
          <div className="alert alert-error" style={{ padding: '12px 16px', fontSize: '13px' }}>{error}</div>
        )}

        <form onSubmit={handleSubmit}>
          {isRegister && (
            <div style={{ marginBottom: '16px' }}>
              <label style={{ 
                display: 'block', 
                marginBottom: '6px', 
                fontWeight: '500', 
                color: '#475569', 
                fontSize: '13px' 
              }}>
                Nom complet
              </label>
              <input
                type="text"
                className="input"
                placeholder="Entrez votre nom"
                value={formData.nom}
                onChange={(e) => setFormData({ ...formData, nom: e.target.value })}
                required
                style={{ padding: '12px 16px', marginBottom: '0' }}
              />
            </div>
          )}

          <div style={{ marginBottom: '16px' }}>
            <label style={{ 
              display: 'block', 
              marginBottom: '6px', 
              fontWeight: '500', 
              color: '#475569', 
              fontSize: '13px' 
            }}>
              Email
            </label>
            <input
              type="email"
              className="input"
              placeholder="votre@email.com"
              value={formData.email}
              onChange={(e) => setFormData({ ...formData, email: e.target.value })}
              required
              style={{ padding: '12px 16px', marginBottom: '0' }}
            />
          </div>

          <div style={{ marginBottom: '20px' }}>
            <label style={{ 
              display: 'block', 
              marginBottom: '6px', 
              fontWeight: '500', 
              color: '#475569', 
              fontSize: '13px' 
            }}>
              Mot de passe
            </label>
            <input
              type="password"
              className="input"
              placeholder="••••••••"
              value={formData.password}
              onChange={(e) => setFormData({ ...formData, password: e.target.value })}
              required
              style={{ padding: '12px 16px', marginBottom: '0' }}
            />
          </div>

          <button
            type="submit"
            className="btn btn-primary"
            style={{ 
              width: '100%', 
              padding: '13px',
              fontSize: '14px',
              fontWeight: '600'
            }}
            disabled={loading}
          >
            {loading ? 'Chargement...' : isRegister ? 'Créer mon compte' : 'Se connecter'}
          </button>

          {!isRegister && (
            <div style={{ textAlign: 'center', marginTop: '16px' }}>
              <a href="#" style={{ 
                color: '#124fa0', 
                fontSize: '12px', 
                textDecoration: 'none',
                fontWeight: '500'
              }}>
                Mot de passe oublié ?
              </a>
            </div>
          )}
        </form>

        <div style={{ 
          textAlign: 'center', 
          paddingTop: '20px',
          marginTop: '20px',
          borderTop: '1px solid #e2e8f0'
        }}>
          <span style={{ color: '#64748b', fontSize: '13px' }}>
            {isRegister ? 'Déjà inscrit ?' : 'Pas de compte ?'}
          </span>
          {' '}
          <button
            onClick={() => {
              setIsRegister(!isRegister)
              setError('')
              setFormData({ nom: '', email: '', password: '' })
            }}
            className="link-button"
            style={{ fontSize: '13px' }}
          >
            {isRegister ? 'Se connecter' : "S'inscrire"}
          </button>
        </div>
      </div>
    </div>
  )
}