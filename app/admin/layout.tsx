import type React from "react"
import { SidebarNav } from "@/components/admin/sidebar-nav"

const sidebarNavItems = [
  {
    title: "Dashboard",
    href: "/admin",
  },
  {
    title: "Books",
    href: "/admin/books",
  },
  {
    title: "Users",
    href: "/admin/users",
  },
  {
    title: "Loans",
    href: "/admin/loans",
  },
  {
    title: "Settings",
    href: "/admin/settings",
  },
]

interface AdminLayoutProps {
  children: React.ReactNode
}

export default function AdminLayout({ children }: AdminLayoutProps) {
  return (
    <div className="container flex-1 items-start md:grid md:grid-cols-[220px_1fr] md:gap-6 lg:grid-cols-[240px_1fr] lg:gap-10">
      <aside className="fixed top-14 z-30 -ml-2 hidden h-[calc(100vh-3.5rem)] w-full shrink-0 md:sticky md:block">
        <div className="h-full py-6 pl-8 pr-6 lg:py-8">
          <SidebarNav items={sidebarNavItems} />
        </div>
      </aside>
      <main className="flex w-full flex-col overflow-hidden py-6">{children}</main>
    </div>
  )
}

