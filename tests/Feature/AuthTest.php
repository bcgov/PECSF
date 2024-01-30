<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{

    public function test_login_redirects_to_home()
      {
        User::updateOrCreate([
          'email' => 'employee1@example.com',
        ],[
          'name' => 'Employee A',
          'password' => bcrypt('employee1@123'),
          'emplid' => '130347'
        ]);

        $response = $this->post('/login', [
          'email' => 'employee1@example.com',
          'password' => bcrypt('employeess1@123'),
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
      }
    
      public function test_unauthenticated_user_cannot_access()
      {
        
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('login');
      }

}
