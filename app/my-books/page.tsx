"use client"

import { useState, useEffect } from "react"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { AlertTriangle, CheckCircle } from "lucide-react"

// Component for the countdown timer
function CountdownTimer({ dueDate }: { dueDate: Date }) {
  const [timeLeft, setTimeLeft] = useState<{
    days: number
    hours: number
    minutes: number
    seconds: number
  }>({ days: 0, hours: 0, minutes: 0, seconds: 0 })

  const [isOverdue, setIsOverdue] = useState(false)

  useEffect(() => {
    const calculateTimeLeft = () => {
      const difference = dueDate.getTime() - new Date().getTime()

      if (difference <= 0) {
        setIsOverdue(true)
        return { days: 0, hours: 0, minutes: 0, seconds: 0 }
      }

      return {
        days: Math.floor(difference / (1000 * 60 * 60 * 24)),
        hours: Math.floor((difference / (1000 * 60 * 60)) % 24),
        minutes: Math.floor((difference / 1000 / 60) % 60),
        seconds: Math.floor((difference / 1000) % 60),
      }
    }

    setTimeLeft(calculateTimeLeft())

    const timer = setInterval(() => {
      setTimeLeft(calculateTimeLeft())
    }, 1000)

    return () => clearInterval(timer)
  }, [dueDate])

  if (isOverdue) {
    return (
      <div className="flex items-center text-destructive font-medium">
        <AlertTriangle className="h-4 w-4 mr-1" />
        Overdue
      </div>
    )
  }

  return (
    <div className="grid grid-cols-4 gap-1 text-center">
      <div className="flex flex-col">
        <span className="text-lg font-bold">{timeLeft.days}</span>
        <span className="text-xs text-muted-foreground">Days</span>
      </div>
      <div className="flex flex-col">
        <span className="text-lg font-bold">{timeLeft.hours}</span>
        <span className="text-xs text-muted-foreground">Hours</span>
      </div>
      <div className="flex flex-col">
        <span className="text-lg font-bold">{timeLeft.minutes}</span>
        <span className="text-xs text-muted-foreground">Mins</span>
      </div>
      <div className="flex flex-col">
        <span className="text-lg font-bold">{timeLeft.seconds}</span>
        <span className="text-xs text-muted-foreground">Secs</span>
      </div>
    </div>
  )
}

export default function MyBooksPage() {
  // This would be replaced with actual data from a database
  const currentBooks = [
    {
      id: 1,
      title: "The Great Gatsby",
      author: "F. Scott Fitzgerald",
      borrowedDate: new Date("2023-03-15"),
      dueDate: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000), // 2 days from now
    },
    {
      id: 2,
      title: "To Kill a Mockingbird",
      author: "Harper Lee",
      borrowedDate: new Date("2023-03-10"),
      dueDate: new Date(Date.now() + 5 * 24 * 60 * 60 * 1000), // 5 days from now
    },
    {
      id: 3,
      title: "1984",
      author: "George Orwell",
      borrowedDate: new Date("2023-03-01"),
      dueDate: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000), // 2 days ago (overdue)
    },
  ]

  const historyBooks = [
    {
      id: 4,
      title: "Pride and Prejudice",
      author: "Jane Austen",
      borrowedDate: new Date("2023-02-15"),
      returnedDate: new Date("2023-03-01"),
    },
    {
      id: 5,
      title: "The Hobbit",
      author: "J.R.R. Tolkien",
      borrowedDate: new Date("2023-01-20"),
      returnedDate: new Date("2023-02-10"),
    },
  ]

  return (
    <div className="container py-10">
      <div className="flex flex-col gap-6">
        <div className="flex flex-col gap-2">
          <h1 className="text-3xl font-bold tracking-tight">My Books</h1>
          <p className="text-muted-foreground">Manage your borrowed books and view your reading history.</p>
        </div>

        <Tabs defaultValue="current" className="w-full">
          <TabsList className="grid w-full grid-cols-2">
            <TabsTrigger value="current">Currently Borrowed</TabsTrigger>
            <TabsTrigger value="history">History</TabsTrigger>
          </TabsList>
          <TabsContent value="current" className="mt-6">
            <div className="grid gap-6">
              {currentBooks.map((book) => (
                <Card key={book.id}>
                  <CardHeader>
                    <CardTitle>{book.title}</CardTitle>
                    <CardDescription>{book.author}</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="grid gap-2">
                      <div className="flex justify-between text-sm">
                        <span className="text-muted-foreground">Borrowed:</span>
                        <span>{book.borrowedDate.toLocaleDateString()}</span>
                      </div>
                      <div className="flex justify-between text-sm">
                        <span className="text-muted-foreground">Due:</span>
                        <span>{book.dueDate.toLocaleDateString()}</span>
                      </div>
                      <div className="mt-4">
                        <div className="text-sm font-medium mb-2">Time Remaining:</div>
                        <CountdownTimer dueDate={book.dueDate} />
                      </div>
                    </div>
                  </CardContent>
                  <CardFooter>
                    <Button className="w-full">Return Book</Button>
                  </CardFooter>
                </Card>
              ))}
            </div>
          </TabsContent>
          <TabsContent value="history" className="mt-6">
            <div className="grid gap-6">
              {historyBooks.map((book) => (
                <Card key={book.id}>
                  <CardHeader>
                    <CardTitle>{book.title}</CardTitle>
                    <CardDescription>{book.author}</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="grid gap-2">
                      <div className="flex justify-between text-sm">
                        <span className="text-muted-foreground">Borrowed:</span>
                        <span>{book.borrowedDate.toLocaleDateString()}</span>
                      </div>
                      <div className="flex justify-between text-sm">
                        <span className="text-muted-foreground">Returned:</span>
                        <span>{book.returnedDate.toLocaleDateString()}</span>
                      </div>
                      <div className="flex items-center justify-center mt-4 text-green-600">
                        <CheckCircle className="h-5 w-5 mr-2" />
                        <span>Returned on time</span>
                      </div>
                    </div>
                  </CardContent>
                  <CardFooter>
                    <Button variant="outline" className="w-full">
                      Borrow Again
                    </Button>
                  </CardFooter>
                </Card>
              ))}
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  )
}

