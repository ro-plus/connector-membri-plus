<?php

namespace App\Console\Commands;

use App\Models\Member;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportPlus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:plus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private $token='';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->anticipate('Email?',['victor.apostol@ro.plus']);
        $password = $this->anticipate('Password?',[env('API_PASS')]);
        $all_members = [];
        if(is_null($email)||is_null($password)){
            $this->error('email or password not provided');
        } else {
            $data = $this->makeRequest('api/login','POST','',['email'=> $email, 'password'=> $password]);
            if(isset($data['success'])&&$data['success']){
                $this->token = $data['data']['token'];
                $members_data = $this->makeRequest('api/users?_page=1&include=role,nbMember,nbProfile,userMembership,membershipFee,sanctions&_per_page=50','GET');
                $bar = $this->output->createProgressBar($members_data['meta']['last_page']);
                for ($i=1;$i<=$members_data['meta']['last_page'];$i++){
                    $page_data = $this->makeRequest('api/users?include=role,nbMember,nbProfile&_per_page=50','GET',$i);
                    if(isset($page_data['data'])){
                        foreach($page_data['data'] as $data){
                            $sm = [];
                            $serie_ci = '';
                            $nr_ci = '';
                            $home_address = '';
                            $active_address = '';
                            $birtdate = '';
                            $cnp = '';
                            $gender = '';
                            $studies = '';
                            $profession = '';
                            $political_experience = '';
                            $citizenship = '';
                            if(isset($data['_includes']['sanctions']['data'])&&count($data['_includes']['sanctions']['data'])>0){
                                dd($data['_includes']['sanctions']['data']);

                            }
                            if(isset($data['_includes']['nbMember']['data']['gender'])){
                                $gender = $data['_includes']['nbMember']['data']['gender'];
                            }
                            if(isset($data['_includes']['nbProfile']['data']['p_fulldata'])){
                                $profile_data = json_decode($data['_includes']['nbProfile']['data']['p_fulldata'],true);
                                if(isset($profile_data['home_address'])){
                                    $home_address = $profile_data['home_address']['address1'].' '.$profile_data['home_address']['address2'].' '.$profile_data['home_address']['address3'].', '.$profile_data['home_address']['city'].', '.$profile_data['home_address']['county'];
                                }
                                if($profile_data['preferati_sa_va_desfasurati_activitatea_in_ro_in_localitatea_si_judetul_in_care_locuiti_']!='Da'&&isset($profile_data['billing_address'])){
                                    $active_address = $profile_data['billing_address']['address1'].' '.$profile_data['billing_address']['address2'].' '.$profile_data['billing_address']['address3'].', '.$profile_data['billing_address']['city'].', '.$profile_data['billing_address']['county'];
                                } else {
                                    $active_address = $home_address;
                                }
                                if(!is_null($profile_data['cont_facebook'])){
                                    $sm[]=$profile_data['cont_facebook'];
                                }
                                if(!is_null($profile_data['cont_twitter'])){
                                    $sm[]=$profile_data['cont_twitter'];
                                }
                                if(!is_null($profile_data['cont_instagram'])){
                                    $sm[]=$profile_data['cont_instagram'];
                                }
                                if(!is_null($profile_data['cont_altele'])){
                                    $sm[]=$profile_data['cont_altele'];
                                }
                                $serie_ci = $profile_data['ci_serie'];
                                $nr_ci = $profile_data['ci_nr'];
                                if(!is_null($profile_data['data_nasterii'])&&$profile_data['data_nasterii']!=''){
                                    try{
                                        $profile_data['data_nasterii'] = str_replace(' ','',$profile_data['data_nasterii']);
                                        if(strpos($profile_data['data_nasterii'], '/')&&!strpos($profile_data['data_nasterii'], '.')){
                                            $birtdate = Carbon::createFromFormat('m/d/Y',$profile_data['data_nasterii']);
                                            $birtdate = $birtdate->format('Y-m-d');
                                        } elseif(strpos($profile_data['data_nasterii'], '.')&&!strpos($profile_data['data_nasterii'], '/')){
                                            $birtdate = Carbon::createFromFormat('m.d.Y',$profile_data['data_nasterii']);
                                            $birtdate = $birtdate->format('Y-m-d');
                                        }
                                    } catch (InvalidFormatException $e){
                                        if(!is_null($profile_data['cnp'])){
                                            $y = substr($profile_data['cnp'],1,2);
                                            if($y<20){
                                                $y="19".$y;
                                            } else {
                                                $y="19".$y;
                                            }
                                            $m = substr($profile_data['cnp'],3,2);
                                            $d = substr($profile_data['cnp'],5,2);
                                            $profile_data['data_nasterii'] = $m."/".$d."/".$y;
                                            $birtdate = Carbon::createFromFormat('m/d/Y',$profile_data['data_nasterii']);
                                            $birtdate = $birtdate->format('Y-m-d');
                                        }
                                    }
                                }
                                if(!is_null($profile_data['cnp'])){
                                    $cnp = $profile_data['cnp'];
                                }
                                if(!is_null($profile_data['studii'])){
                                    $studies = $profile_data['studii'];
                                }
                                if(!is_null($profile_data['profesie'])){
                                    $profession = $profile_data['profesie'];
                                }
                                if($profile_data['ati_fost_membru_a_in_al_e_partid_e_politice_']==='Da'){
                                    $political_experience = $profile_data['partidul'].' '.$profile_data['perioada'].' '.$profile_data['functii_in_partid'];
                                }
                                if(!is_null($profile_data['cetatenia'])){
                                    $citizenship = $profile_data['cetatenia'];
                                }
                            }
                            $member_fee_paid_until = null;
                            if(isset($data['_includes']['userMembership']['data']['m_expires_on'])){
                                $member_fee_paid_until = $data['_includes']['userMembership']['data']['m_expires_on'];
                            }
                            $r_community = '';
                            $r_subsidiary = '';
                            $r_genplus = '';
                            $r_region = '';
                            if(isset($data['_includes']['role']['data'])){
                                $r_community = $data['_includes']['role']['data']['r_community'];
                                $r_subsidiary = $data['_includes']['role']['data']['r_subsidiary'];
                                $r_genplus = $data['_includes']['role']['data']['r_genplus'];
                                $r_region = $data['_includes']['role']['data']['r_region'];
                            }
                            $last_document_nr = '';
                            $date_last_document_nr = null;
                            if(isset($data['_includes']['membershipFee']['data'])&&count($data['_includes']['membershipFee']['data'])>0){
                                $last_document_nr = $data['_includes']['membershipFee']['data'][0]['Document_nr'];
                                $date_last_document_nr = $data['_includes']['membershipFee']['data'][0]['Data'];
                            }
                            $test = [
                                'nb_id'         => $data['nb_id'],
                                'last_name'     => $data['last_name'],
                                'first_name'    => $data['first_name'],
                                'formation'     => 'PLUS',
                                'county_short'  => substr($data['cl_plus'],0,2),
                                'status'        => 'activ',
                                'org'           => $data['cl_plus'],
                                'home_address'  => $home_address,
                                'active_address'=> $active_address,
                                'phone'         => $data['mobile'],
                                'email'         => $data['email'],
                                'social_media'          => json_encode($sm),
                                'serie_ci'              => $serie_ci,
                                'nr_ci'                 => $nr_ci,
                                'cnp'                   => $cnp,
                                'gender'                => $gender,
                                'birthdate'             => $birtdate,
                                'profession'            => $profession,
                                'studies'               => $studies,
                                'political_experience'  => $political_experience,
                                'areas_of_interest'     => '',
                                'citizenship'           => $citizenship,
                                'started_on'            => $data['started_on'],
                                'member_fee_paid_until' => $member_fee_paid_until,
                                'r_community'           => $r_community,
                                'r_subsidiary'          => $r_subsidiary,
                                'r_genplus'             => $r_genplus,
                                'r_region'              => $r_region,
                                'last_document_nr'      => $last_document_nr,
                                'date_last_document_nr' => $date_last_document_nr
                            ];
                            $test['signature'] = md5(json_encode($test));
                            $ch = Member::where('nb_id',$test['nb_id'])->first();
                            if($ch){
                                if($ch->signature!=$test['signature']){
                                    $ch->update($test);
                                }
                            } else {
                                Member::create($test);
                            }
                            unset($test);
                        }
                    }
                    unset($page_data);
                    $bar->advance();
                }
            }
            else{
                $this->error('invalid email or password');
            }
        }
    }

    private function makeRequest($url,$type,$page='',$params=[])
    {
        $api_url = env('API_URL');
        $url = $api_url.$url;
        if($page!=''){
            $url = $url.'&_page='.$page;
        }
        if($type==='GET'){
            $request = Http::withToken($this->token)->get($url);
        } else {
            $request = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post($url, $params);
        }
        $response = $request->getBody()->getContents();
        $json = json_decode($response,true);
        if(is_null($json)){
            dd($response);
        }
        return json_decode($response,true);
    }
}
