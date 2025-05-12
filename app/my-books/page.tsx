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
      coverImage: "/images/book-covers/great-gatsby.jpg",
      borrowedDate: new Date("2023-03-15"),
      dueDate: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000), // 2 days from now
    },
    {
      id: 2,
      title: "To Kill a Mockingbird",
      author: "Harper Lee",
      coverImage: "/images/book-covers/mockingbird.jpg",
      borrowedDate: new Date("2023-03-10"),
      dueDate: new Date(Date.now() + 5 * 24 * 60 * 60 * 1000), // 5 days from now
    },
    {
      id: 3,
      title: "1984",
      author: "George Orwell",
      coverImage: "/images/book-covers/1984.jpg",
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
      returnedOnTime: true,
    },
    {
      id: 5,
      title: "The Hobbit",
      author: "J.R.R. Tolkien",
      borrowedDate: new Date("2023-01-20"),
      returnedDate: new Date("2023-02-10"),
      returnedOnTime: true,
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
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              {currentBooks.map((book) => (
                <Card key={book.id}>
                  <CardHeader className="pb-2">
                    <CardTitle className="text-lg">{book.title}</CardTitle>
                    <CardDescription>{book.author}</CardDescription>
                  </CardHeader>
                  <div className="flex justify-center p-4 bg-muted/30">
                    {book.coverImage ? (
                      <img 
                        src={book.coverImage} 
                        alt={book.title} 
                        className="h-[150px] object-contain"
                      />
                    ) : (
                      <div className="flex flex-col items-center justify-center text-muted-foreground h-[150px] w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                          <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                        </svg>
                        <p className="text-xs mt-2">No cover available</p>
                      </div>
                    )}
                  </div>
                  <CardContent className="pt-4">
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
            <div className="rounded-md border">
              <table className="w-full">
                <thead>
                  <tr className="border-b bg-muted/50">
                    <th className="h-10 px-4 text-left font-medium">Book</th>
                    <th className="h-10 px-4 text-left font-medium">Borrowed</th>
                    <th className="h-10 px-4 text-left font-medium">Returned</th>
                    <th className="h-10 px-4 text-left font-medium">Status</th>
                    <th className="h-10 px-4 text-right font-medium">Action</th>
                  </tr>
                </thead>
                <tbody>
                  {historyBooks.map((book) => (
                    <tr key={book.id} className="border-b">
                      <td className="p-4">
                        <div className="font-medium">{book.title}</div>
                        <div className="text-sm text-muted-foreground">{book.author}</div>
                      </td>
                      <td className="p-4">{book.borrowedDate.toLocaleDateString()}</td>
                      <td className="p-4">{book.returnedDate.toLocaleDateString()}</td>
                      <td className="p-4">
                        {book.returnedOnTime ? (
                          <div className="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-green-50 text-green-600">
                            <CheckCircle className="h-3 w-3 mr-1" /> Returned on time
                          </div>
                        ) : (
                          <div className="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-red-50 text-red-600">
                            <AlertTriangle className="h-3 w-3 mr-1" /> Returned late
                          </div>
                        )}
                      </td>
                      <td className="p-4 text-right">
                        <Button variant="outline" size="sm">
                          Borrow Again
                        </Button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  )
}

