<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class SampleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User Creation

        $users = [
            [ 'id'=>101, 'email'=>'employee101@example.com', 'name'=>'Employee 101', 'emplid' => '005829', ],
            [ 'id'=>102, 'email'=>'employee102@example.com', 'name'=>'Employee 102', 'emplid' => '010036', ],
            [ 'id'=>103, 'email'=>'employee103@example.com', 'name'=>'Employee 103', 'emplid' => '013238', ],
            [ 'id'=>104, 'email'=>'employee104@example.com', 'name'=>'Employee 104', 'emplid' => '016603', ],
            [ 'id'=>105, 'email'=>'employee105@example.com', 'name'=>'Employee 105', 'emplid' => '018522', ],
            [ 'id'=>106, 'email'=>'employee106@example.com', 'name'=>'Employee 106', 'emplid' => '026277', ],
            [ 'id'=>107, 'email'=>'employee107@example.com', 'name'=>'Employee 107', 'emplid' => '027290', ],
            [ 'id'=>108, 'email'=>'employee108@example.com', 'name'=>'Employee 108', 'emplid' => '037562', ],
            [ 'id'=>109, 'email'=>'employee109@example.com', 'name'=>'Employee 109', 'emplid' => '044030', ],
            [ 'id'=>110, 'email'=>'employee110@example.com', 'name'=>'Employee 110', 'emplid' => '055564', ],
            [ 'id'=>111, 'email'=>'employee111@example.com', 'name'=>'Employee 111', 'emplid' => '056965', ],
            [ 'id'=>112, 'email'=>'employee112@example.com', 'name'=>'Employee 112', 'emplid' => '060242', ],
            [ 'id'=>113, 'email'=>'employee113@example.com', 'name'=>'Employee 113', 'emplid' => '061205', ],
            [ 'id'=>114, 'email'=>'employee114@example.com', 'name'=>'Employee 114', 'emplid' => '069956', ],
            [ 'id'=>115, 'email'=>'employee115@example.com', 'name'=>'Employee 115', 'emplid' => '085102', ],
            [ 'id'=>116, 'email'=>'employee116@example.com', 'name'=>'Employee 116', 'emplid' => '097590', ],
            [ 'id'=>117, 'email'=>'employee117@example.com', 'name'=>'Employee 117', 'emplid' => '098773', ],
            [ 'id'=>118, 'email'=>'employee118@example.com', 'name'=>'Employee 118', 'emplid' => '098998', ],
            [ 'id'=>119, 'email'=>'employee119@example.com', 'name'=>'Employee 119', 'emplid' => '099489', ],
            [ 'id'=>120, 'email'=>'employee120@example.com', 'name'=>'Employee 120', 'emplid' => '099819', ],
            [ 'id'=>121, 'email'=>'employee121@example.com', 'name'=>'Employee 121', 'emplid' => '099992', ],
            [ 'id'=>122, 'email'=>'employee122@example.com', 'name'=>'Employee 122', 'emplid' => '100987', ],
            [ 'id'=>123, 'email'=>'employee123@example.com', 'name'=>'Employee 123', 'emplid' => '101014', ],
            [ 'id'=>124, 'email'=>'employee124@example.com', 'name'=>'Employee 124', 'emplid' => '104181', ],
            [ 'id'=>125, 'email'=>'employee125@example.com', 'name'=>'Employee 125', 'emplid' => '104191', ],
            [ 'id'=>126, 'email'=>'employee126@example.com', 'name'=>'Employee 126', 'emplid' => '105083', ],
            [ 'id'=>127, 'email'=>'employee127@example.com', 'name'=>'Employee 127', 'emplid' => '105926', ],
            [ 'id'=>128, 'email'=>'employee128@example.com', 'name'=>'Employee 128', 'emplid' => '106994', ],
            [ 'id'=>129, 'email'=>'employee129@example.com', 'name'=>'Employee 129', 'emplid' => '107460', ],
            [ 'id'=>130, 'email'=>'employee130@example.com', 'name'=>'Employee 130', 'emplid' => '108318', ],
            [ 'id'=>131, 'email'=>'employee131@example.com', 'name'=>'Employee 131', 'emplid' => '110022', ],
            [ 'id'=>132, 'email'=>'employee132@example.com', 'name'=>'Employee 132', 'emplid' => '110706', ],
            [ 'id'=>133, 'email'=>'employee133@example.com', 'name'=>'Employee 133', 'emplid' => '111179', ],
            [ 'id'=>134, 'email'=>'employee134@example.com', 'name'=>'Employee 134', 'emplid' => '112987', ],
            [ 'id'=>135, 'email'=>'employee135@example.com', 'name'=>'Employee 135', 'emplid' => '120539', ],
            [ 'id'=>136, 'email'=>'employee136@example.com', 'name'=>'Employee 136', 'emplid' => '122510', ],
            [ 'id'=>137, 'email'=>'employee137@example.com', 'name'=>'Employee 137', 'emplid' => '123284', ],
            [ 'id'=>138, 'email'=>'employee138@example.com', 'name'=>'Employee 138', 'emplid' => '125662', ],
            [ 'id'=>139, 'email'=>'employee139@example.com', 'name'=>'Employee 139', 'emplid' => '126390', ],
            [ 'id'=>140, 'email'=>'employee140@example.com', 'name'=>'Employee 140', 'emplid' => '127365', ],
            [ 'id'=>141, 'email'=>'employee141@example.com', 'name'=>'Employee 141', 'emplid' => '128123', ],
            [ 'id'=>142, 'email'=>'employee142@example.com', 'name'=>'Employee 142', 'emplid' => '129245', ],
            [ 'id'=>143, 'email'=>'employee143@example.com', 'name'=>'Employee 143', 'emplid' => '130597', ],
            [ 'id'=>144, 'email'=>'employee144@example.com', 'name'=>'Employee 144', 'emplid' => '131310', ],
            [ 'id'=>145, 'email'=>'employee145@example.com', 'name'=>'Employee 145', 'emplid' => '132458', ],
            [ 'id'=>146, 'email'=>'employee146@example.com', 'name'=>'Employee 146', 'emplid' => '132544', ],
            [ 'id'=>147, 'email'=>'employee147@example.com', 'name'=>'Employee 147', 'emplid' => '133818', ],
            [ 'id'=>148, 'email'=>'employee148@example.com', 'name'=>'Employee 148', 'emplid' => '134625', ],
            [ 'id'=>149, 'email'=>'employee149@example.com', 'name'=>'Employee 149', 'emplid' => '135127', ],
            [ 'id'=>150, 'email'=>'employee150@example.com', 'name'=>'Employee 150', 'emplid' => '137130', ],
            [ 'id'=>151, 'email'=>'employee151@example.com', 'name'=>'Employee 151', 'emplid' => '137319', ],
            [ 'id'=>152, 'email'=>'employee152@example.com', 'name'=>'Employee 152', 'emplid' => '137942', ],
            [ 'id'=>153, 'email'=>'employee153@example.com', 'name'=>'Employee 153', 'emplid' => '138140', ],
            [ 'id'=>154, 'email'=>'employee154@example.com', 'name'=>'Employee 154', 'emplid' => '138174', ],
            [ 'id'=>155, 'email'=>'employee155@example.com', 'name'=>'Employee 155', 'emplid' => '140618', ],
            [ 'id'=>156, 'email'=>'employee156@example.com', 'name'=>'Employee 156', 'emplid' => '140717', ],
            [ 'id'=>157, 'email'=>'employee157@example.com', 'name'=>'Employee 157', 'emplid' => '140903', ],
            [ 'id'=>158, 'email'=>'employee158@example.com', 'name'=>'Employee 158', 'emplid' => '140996', ],
            [ 'id'=>159, 'email'=>'employee159@example.com', 'name'=>'Employee 159', 'emplid' => '142516', ],
            [ 'id'=>160, 'email'=>'employee160@example.com', 'name'=>'Employee 160', 'emplid' => '143222', ],
            [ 'id'=>161, 'email'=>'employee161@example.com', 'name'=>'Employee 161', 'emplid' => '143836', ],
            [ 'id'=>162, 'email'=>'employee162@example.com', 'name'=>'Employee 162', 'emplid' => '144511', ],
            [ 'id'=>163, 'email'=>'employee163@example.com', 'name'=>'Employee 163', 'emplid' => '144835', ],
            [ 'id'=>164, 'email'=>'employee164@example.com', 'name'=>'Employee 164', 'emplid' => '145336', ],
            [ 'id'=>165, 'email'=>'employee165@example.com', 'name'=>'Employee 165', 'emplid' => '146293', ],
            [ 'id'=>166, 'email'=>'employee166@example.com', 'name'=>'Employee 166', 'emplid' => '147367', ],
            [ 'id'=>167, 'email'=>'employee167@example.com', 'name'=>'Employee 167', 'emplid' => '148407', ],
            [ 'id'=>168, 'email'=>'employee168@example.com', 'name'=>'Employee 168', 'emplid' => '148981', ],
            [ 'id'=>169, 'email'=>'employee169@example.com', 'name'=>'Employee 169', 'emplid' => '149964', ],
            [ 'id'=>170, 'email'=>'employee170@example.com', 'name'=>'Employee 170', 'emplid' => '151155', ],
            [ 'id'=>171, 'email'=>'employee171@example.com', 'name'=>'Employee 171', 'emplid' => '151721', ],
            [ 'id'=>172, 'email'=>'employee172@example.com', 'name'=>'Employee 172', 'emplid' => '152576', ],
            [ 'id'=>173, 'email'=>'employee173@example.com', 'name'=>'Employee 173', 'emplid' => '154168', ],
            [ 'id'=>174, 'email'=>'employee174@example.com', 'name'=>'Employee 174', 'emplid' => '154280', ],
            [ 'id'=>175, 'email'=>'employee175@example.com', 'name'=>'Employee 175', 'emplid' => '156509', ],
            [ 'id'=>176, 'email'=>'employee176@example.com', 'name'=>'Employee 176', 'emplid' => '157903', ],
            [ 'id'=>177, 'email'=>'employee177@example.com', 'name'=>'Employee 177', 'emplid' => '157911', ],
            [ 'id'=>178, 'email'=>'employee178@example.com', 'name'=>'Employee 178', 'emplid' => '158816', ],
            [ 'id'=>179, 'email'=>'employee179@example.com', 'name'=>'Employee 179', 'emplid' => '158864', ],
            [ 'id'=>180, 'email'=>'employee180@example.com', 'name'=>'Employee 180', 'emplid' => '159065', ],
            [ 'id'=>181, 'email'=>'employee181@example.com', 'name'=>'Employee 181', 'emplid' => '165705', ],
            [ 'id'=>182, 'email'=>'employee182@example.com', 'name'=>'Employee 182', 'emplid' => '167254', ],
            [ 'id'=>183, 'email'=>'employee183@example.com', 'name'=>'Employee 183', 'emplid' => '170161', ],
            [ 'id'=>184, 'email'=>'employee184@example.com', 'name'=>'Employee 184', 'emplid' => '170591', ],
            [ 'id'=>185, 'email'=>'employee185@example.com', 'name'=>'Employee 185', 'emplid' => '171040', ],
            [ 'id'=>186, 'email'=>'employee186@example.com', 'name'=>'Employee 186', 'emplid' => '171123', ],
            [ 'id'=>187, 'email'=>'employee187@example.com', 'name'=>'Employee 187', 'emplid' => '171229', ],
            [ 'id'=>188, 'email'=>'employee188@example.com', 'name'=>'Employee 188', 'emplid' => '171311', ],
            [ 'id'=>189, 'email'=>'employee189@example.com', 'name'=>'Employee 189', 'emplid' => '171902', ],
            [ 'id'=>190, 'email'=>'employee190@example.com', 'name'=>'Employee 190', 'emplid' => '174089', ],
            [ 'id'=>191, 'email'=>'employee191@example.com', 'name'=>'Employee 191', 'emplid' => '174302', ],
            [ 'id'=>192, 'email'=>'employee192@example.com', 'name'=>'Employee 192', 'emplid' => '174932', ],
            [ 'id'=>193, 'email'=>'employee193@example.com', 'name'=>'Employee 193', 'emplid' => '175217', ],
            [ 'id'=>194, 'email'=>'employee194@example.com', 'name'=>'Employee 194', 'emplid' => '180151', ],
            [ 'id'=>195, 'email'=>'employee195@example.com', 'name'=>'Employee 195', 'emplid' => '187738', ],
            [ 'id'=>196, 'email'=>'employee196@example.com', 'name'=>'Employee 196', 'emplid' => '192011', ],
            [ 'id'=>197, 'email'=>'employee197@example.com', 'name'=>'Employee 197', 'emplid' => '192018', ],
            [ 'id'=>198, 'email'=>'employee198@example.com', 'name'=>'Employee 198', 'emplid' => '192042', ],
            [ 'id'=>199, 'email'=>'employee199@example.com', 'name'=>'Employee 199', 'emplid' => '192048', ],
            [ 'id'=>200, 'email'=>'employee200@example.com', 'name'=>'Employee 200', 'emplid' => '192055', ],
        ];

        $organization = Organization::where('code','GOV')->first();

        foreach ($users as $user) {

          User::updateOrCreate([
                'email' => $user['email'],
          ], [
            'id' => $user['id'],
            'name' => $user['name'],
            'password' => '$2y$10$AyzSQf4vtA/sEFXG1OWdJ.OqoOmkTNpVl4m.u9np9UE/j1HnJa0Ti', 
            'source_type' => 'LCL',
            'organization_id' => $organization ? $organization->id : null,
            'emplid' => $user['emplid'],
          ]);

        }

    }
}
