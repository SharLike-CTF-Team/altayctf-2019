<?php

namespace App\Http\Controllers;

use App\Dialogs;
use App\Friend;
use App\Message;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        return view('home');
    }


    public function news()
    {
        $posts = Post::getNews(Auth::user()->id);
        return view('news', ["posts" => $posts]);
    }


    public function profile(int $id)
    {
        $user = User::findOrFail($id);


        $user->birthday = User::getAge($user->birthday);

        $wallPosts = Post::getWall($id);

        $friends_count = count(DB::table("friends")->where([["id_owner", "=", $user->id], ["approve", "=", "1"]])->orWhere([["approve", "=", "1"], ["id_subject", "=", $user->id]])->get());
        return view('profile', ["user" => $user, "posts" => $wallPosts, "friends_count" => $friends_count]);
    }


    public function addPost(Request $request, int $id)
    {
        $owner = User::findOrFail(Auth::user()->id);
        $recipient = User::findOrFail($id);

        if ($owner->checkFriendship($recipient)) {
            $this->validate($request, [
                'text' => 'string|required',
                'file' => 'image|nullable'
            ], ["text_required" => "Вы не ввели текст сообщения", "file_image" => "Файл не является картинкой"]);

            $post = new Post();
            $post->id_owner = $owner->id;
            $post->id_recipient = $recipient->id;


            $post->text = $request->get("text");


            $file = $request->file('file');

            if (!empty($file)) {
                //Move Uploaded File
                $destinationPath = 'img/uploads';

                $newFileName = md5($file->getClientOriginalName());
                $file->move($destinationPath, $newFileName);

                $post->image = $destinationPath . "/" . $newFileName;
            } else $post->image = null;

            $post->save();

            return redirect("profile/{$recipient->id}");
        } else {
            return redirect("profile/{$recipient->id}")
                ->withErrors(["not_friends" => "Вы не являетесь другом этому пользователю"]);
        }
    }


    public function friends()
    {
        $id_user = Auth::user()->id;

        //friends
        $friends = DB::select("select * from users where id in (select id_owner from friends 
          where id_subject= ? and approve=1 union select id_subject from friends where id_owner= ?
            and approve=1)",[$id_user,$id_user]);

        foreach ($friends as $key => $friend) {
            if (!empty($friend->birthday))
                $friends[$key]->birthday = User::getAge($friend->birthday);
        }

        //requests

        $requests = DB::select("select * from users where id in (select id_owner from friends 
          where id_subject= ? and approve=0)",[$id_user]);

        foreach ($requests as $key => $request) {
            if (!empty($request->birthday))
                $requests[$key]->birthday = User::getAge($request->birthday);
        }


        return view('friends', ["friends" => $friends, "requests" => $requests]);
    }

    public function addFriend(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'numeric|required',
        ], ["digits" => "Неверный id пользователя"]);

        $friend = Friend::where("id_owner", $request->get("user_id"))->where("id_subject", Auth::user()->id)->first();

        if (!empty($friend)) {
            if ($friend->approve === 1) {
                return redirect("profile/{$request->get("user_id")}")
                    ->withErrors(["yet_friends" => "Вы уже друг этому пользователю"]);
            } else {
                $friend->approve = 1;
                $friend->save();

                return redirect("profile/{$request->get("user_id")}");
            }
        } else {
            $friend = new Friend();
            $friend->id_owner = Auth::user()->id;
            $friend->id_subject = $request->get("user_id");
            $friend->approve = 0;
            $friend->save();
        }

        return redirect("profile/{$request->get("user_id")}");
    }

    public function removeFriend(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'numeric|required',
        ], ["digits" => "Неверный id пользователя"]);

        $friend = Friend::where("id_owner", $request->get("user_id"))->where("id_subject", Auth::user()->id)->first();

        if (empty($friend)) {
            $friend = Friend::where("id_subject", $request->get("user_id"))->where("id_owner", Auth::user()->id)->first();
        }

        if (!empty($friend)) {
            $friend->delete();

            return redirect("profile/{$request->get("user_id")}");

        } else {
            return redirect("profile/{$request->get("user_id")}")
                ->withErrors(["yet_friends" => "Вы не друг этому пользователю"]);
        }

        return redirect("profile/{$request->get("user_id")}");
    }


    public function messages(Request $request, string $token = null)
    {
        $dialogs = Dialogs::where("id_owner", Auth::user()->id)->orWhere("id_subject", Auth::user()->id)->orderBy("id", "DESC")->get();
        $users = [];

        foreach ($dialogs as $dialog) {
            if (Auth::user()->id === $dialog->id_owner):
                $user = User::find($dialog->id_subject);
                if (!empty($user->name)) {
                    $name = $user->name . " " . $user->surname;
                } else {
                    $name = $user->login;
                }
                $user_id = $user->id;
                $avatar = $user->avatar;
            else:
                $user = User::find($dialog->id_owner);
                if (!empty($user->name)) {
                    $name = $user->name . " " . $user->surname;
                } else {
                    $name = $user->login;
                }
                $user_id = $user->id;
                $avatar = $user->avatar;
            endif;

            $users[$dialog->id]["id"] = $user_id;
            $users[$dialog->id]["name"] = $name;
            $users[$dialog->id]["avatar"] = $avatar;
        }

        if (empty($token)) {
            return view("messages", ["dialogs" => $dialogs, "users" => $users]);
        } else {
            $current_dialog = Dialogs::where("slug", $token)->first();
            if (!empty($current_dialog)) {
                $messages = Message::where("id_dialogs", $current_dialog->id)->orderBy("id", "DESC")->get();
                return view("messages", ["messages" => $messages, "dialogs" => $dialogs, "users" => $users,
                    "current_dialog" => $current_dialog]);
            } else {
                return view("messages", ["dialogs" => $dialogs, "users" => $users]);
            }
        }
    }


    public function addMessage(Request $request)
    {
        $owner = User::findOrFail(Auth::user()->id);
        $id = $request->get("user_id");
        $subject = User::findOrFail($id);

        if ($owner->checkFriendship($subject)) {

            $token1 = Dialogs::generateToken($owner, $subject);
            $token2 = Dialogs::generateToken($subject, $owner);

            if (!empty($dialog = Dialogs::where("slug", $token2)->first())) {
                $token = $token2;
            } else {
                $token = $token1;
            }

            $dialog = Dialogs::where("slug", $token)->first();

            if (empty($dialog)) {
                $dialog = new Dialogs();
                $dialog->id_owner = $owner->id;
                $dialog->id_subject = $subject->id;
                $dialog->slug = $token;
                $dialog->save();
            }

            if (!empty($request->get("text"))) {
                $this->validate($request, [
                    'text' => 'string|required',
                    'file' => 'image|nullable'
                ], ["text_required" => "Вы не ввели текст сообщения", "file_image" => "Файл не является картинкой"]);
                $message = new Message();
                $message->id_owner = $owner->id;
                $message->id_dialogs = $dialog->id;


                $message->text = $request->get("text");


                $file = $request->file('file');

                if (!empty($file)) {
                    //Move Uploaded File
                    $destinationPath = 'img/uploads';

                    $newFileName = md5($file->getClientOriginalName());
                    $file->move($destinationPath, $newFileName);

                    $message->image = $destinationPath . "/" . $newFileName;
                } else $message->image = null;

                $message->save();
            }
            return redirect("messages/{$dialog->slug}");
        } else {
            return redirect("profile/{$subject->id}")
                ->withErrors(["not_friends" => "Вы не являетесь другом этому пользователю"]);
        }
    }


    public function account()
    {
        $user = Auth::user();
        $user->birthday = date("Y-m-d", strtotime($user->birthday));

        return view("account", ["user" => $user]);
    }

    public function editProfile(Request $request)
    {
        $this->validate($request, [
            'name' => ['string', 'max:255', 'nullable'],
            'surname' => ['string', 'max:255', 'nullable'],
            'race' => ['string', 'max:255', 'nullable'],
            'gender' => ['string', 'max:255', 'nullable'],
            'birthday' => ['date', 'nullable'],
            'homeplace' => ['string', 'max:255', 'nullable'],
            'file' => ['image', 'nullable'],
            'selfdescription' => ["string", 'nullable']
        ]);

        $user = User::find(Auth::user()->id);

        $user->name = $request->get("name");
        $user->surname = $request->get("surname");
        $user->race = $request->get("race");
        $user->gender = $request->get("gender");
        $user->birthday = $request->get("birthday");
        $user->homeplace = $request->get("homeplace");
        $user->selfdescription = $request->get("selfdescription");

        $file = $request->file('file');

        if (!empty($file)) {
            //Move Uploaded File
            $destinationPath = 'img/uploads';

            $newFileName = md5($file->getClientOriginalName());
            $file->move($destinationPath, $newFileName);

            $avatar = $destinationPath . "/" . $newFileName;
        } else $avatar = $user->avatar;


        $user->avatar = $avatar;

        $user->save();

        return redirect("account")->with("message", "Вы успешно изменили данные");
    }

    public function changePasswd(Request $request)
    {
        $this->validate($request, [
            'oldpassword' => ['required', 'string', 'min:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $oldpasswd = $request->get("oldpassword");
        $passwd = $request->get("password");
        $user = User::where("login", $request->get("login"))->first();
        if (!empty($user)) {
            if (Hash::check($oldpasswd, Auth::user()->password)) {
                $user->password = Hash::make($passwd);
                $user->save();
                return redirect("account")->with("message", "Пароль успешно изменен");
            }
            return redirect("account")->withErrors(["error_passwd" => "Неверный текущий пароль"]);
        }
        return redirect("account")->withErrors(["error_passwd" => "Пользователя с таким логином нет"]);

    }

    public function users()
    {
        $users = User::all();


        foreach ($users as $key =>$user) {
            if (!empty($user->birthday))
                $users[$key]->birthday = User::getAge($user->birthday);
        }
        return view("users",["users"=>$users]);
    }
}
