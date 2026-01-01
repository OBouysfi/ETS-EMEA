'use client'

import { useEffect } from 'react'
import { useRouter } from 'next/navigation'
import { isAuthenticated } from '@/lib/auth'

export default function Home() {
  const router = useRouter()

  useEffect(() => {
    if (isAuthenticated()) {
      router.push('/sessions')
    } else {
      router.push('/login')
    }
  }, [router])

  return (
    <div className="container">
      <div className="card">
        <h1>Redirection...</h1>
      </div>
    </div>
  )
}