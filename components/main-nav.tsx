"use client"
import Link from "next/link"
import { usePathname } from "next/navigation"
import { cn } from "@/lib/utils"
import { BookOpen } from "lucide-react"

export function MainNav() {
  const pathname = usePathname()

  return (
    <div className="mr-4 flex">
      <Link href="/" className="mr-6 flex items-center space-x-2">
        <BookOpen className="h-6 w-6" />
        <span className="hidden font-bold sm:inline-block">Library App</span>
      </Link>
      <nav className="flex items-center space-x-6 text-sm font-medium">
        <Link
          href="/"
          className={cn(
            "transition-colors hover:text-foreground/80",
            pathname === "/" ? "text-foreground" : "text-foreground/60",
          )}
        >
          Home
        </Link>
        <Link
          href="/books"
          className={cn(
            "transition-colors hover:text-foreground/80",
            pathname?.startsWith("/books") ? "text-foreground" : "text-foreground/60",
          )}
        >
          Browse Books
        </Link>
        <Link
          href="/my-books"
          className={cn(
            "transition-colors hover:text-foreground/80",
            pathname?.startsWith("/my-books") ? "text-foreground" : "text-foreground/60",
          )}
        >
          My Books
        </Link>
        <Link
          href="/admin"
          className={cn(
            "transition-colors hover:text-foreground/80",
            pathname?.startsWith("/admin") ? "text-foreground" : "text-foreground/60",
          )}
        >
          Admin
        </Link>
      </nav>
    </div>
  )
}

