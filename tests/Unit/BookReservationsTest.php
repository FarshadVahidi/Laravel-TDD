<?php

namespace Tests\Unit;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;

class BookReservationsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function a_book_can_be_checked_out()
    {
        //$book->checkout($user);   or   //$user->checkout($book);
        //I start with first one

        $book = Book::factory()->create();
        $user = User::factory()->create();

        $book->checkout($user);

        $this->assertCount(1, Reservation::all());
        $this->assertEquals($user->id, Reservation::first()->user_id);
        $this->assertEquals($book->id, Reservation::first()->book_id);
        $this->assertEquals(now(), Reservation::first()->checked_out_at);
    }

    /** @test */
    public function a_book_can_be_returned()
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();
        $book->checkout($user);

        $book->checkin($user);

        $this->assertCount(1, Reservation::all());
        $this->assertEquals($user->id, Reservation::first()->user_id);
        $this->assertEquals($book->id, Reservation::first()->book_id);
        //$this->assertNotNull(Reservation::first()->checked_in_at);
        $this->assertEquals(now(), Reservation::first()->checked_in_at);
    }

    //a user can checked out a book twice
    /** @test */
    public function a_user_can_check_out_a_book_twice()
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();
        $book->checkout($user);

        $book->checkin($user);

        $book->checkout($user);

        $this->assertCount(2, Reservation::all());
        $this->assertEquals($user->id, Reservation::find(2)->user_id);
        $this->assertEquals($book->id, Reservation::find(2)->book_id);
        $this->assertNull(Reservation::find(2)->checked_in_at);
        $this->assertEquals(now(), Reservation::find(2)->checked_out_at);

        $book->checkin($user);

        $this->assertCount(2, Reservation::all());
        $this->assertEquals($user->id, Reservation::find(2)->user_id);
        $this->assertEquals($book->id, Reservation::find(2)->book_id);
        $this->assertNotNull(Reservation::find(2)->checked_in_at);
        $this->assertEquals(now(), Reservation::find(2)->checked_in_at);


    }

    //if not checked out, then exception
    /** @test */
    public function if_not_checked_out_exception_is_thrown()
    {
        $this->expectException(\Exception::class);

        $book = Book::factory()->create();
        $user = User::factory()->create();

        $book->checkin($user);
    }
}
