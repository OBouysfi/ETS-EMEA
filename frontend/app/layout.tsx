import type { Metadata } from 'next'
import './globals.css'

export const metadata: Metadata = {
  title: 'ETS EMEA - Test Booking',
  description: 'Réservation de sessions de tests de langues',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="fr">
      <body>{children}</body>
    </html>
  )
}