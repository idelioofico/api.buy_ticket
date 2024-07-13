<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventTopic;
use App\Models\EventType;
use App\Models\Logs;
use App\Scopes\CompanyScope;
use Exception;
use Illuminate\Http\Request;

class EventControllerApi extends Controller
{


        public function index(Request $request)
        {
                $response = array(
                        'message' => 'Oops, não foram encontrados eventos para a "' . $request->param . '" ',
                        'status' => $status = 200,
                        'data' => [],
                );

                try {

                        $param = $request->param;
                        $query = Event::with('province', 'event_type', 'producer', 'topic');

                        if (!empty($param)) {

                                $category = EventType::where('name', 'like', "%$param%")->first();

                                Logs::create(
                                        [
                                                'action' => 'search_category',
                                                'request' => json_encode($param),
                                                'response' => json_encode($category),
                                                'ip' => $request->ip(),
                                                'user' => ''
                                        ]
                                );

                                $topic = EventTopic::where('name', 'like', "%$param%")->first();

                                Logs::create(
                                        [
                                                'action' => 'search_topic',
                                                'request' => json_encode($param),
                                                'response' => json_encode($topic),
                                                'ip' => $request->ip(),
                                                'user' => ''
                                        ]
                                );

                                if (!empty($category) && empty($topic)) {
                                        $events = $query->where('event_type_id', $category->id)->get();
                                } else if (empty($category) && !empty($topic)) {

                                        $events = $query->where('topic_id', $topic->id)->get();
                                } else if (!empty($category) && !empty($topic)) {
                                        $events = $query->where('event_type_id', $category->id)->orWhere('topic_id', $topic->id)->get();
                                } else if (empty($categories) && empty($topic)) {
                                        $events = $query->where('title','like',$request->param)->get();
                                }
                                
                        } else {

                                $events = Event::with('province', 'event_type', 'producer', 'topic')->get();
                        }

                        if (!empty($events) && count($events) > 0) {

                                $response = array(
                                        'message' => count($events) . ' eventos listados!',
                                        'status' => $status = 200,
                                        'data' => $events,
                                );

                        } else {

                                $response = array(
                                        'message' => "Oops, nenhum evento foi encontrado!",
                                        'status' => $status = 404,
                                        'data' => [],
                                );

                        }
                } catch (Exception $ex) {

                        $response = array(
                                'message' => 'Ocorreu um erro ao tentar buscar eventos.',
                                'status' => $status = 501,
                                'data' => null,
                                'exception' => $ex->getMessage()
                        );
                }

                Logs::create(
                        [
                                'action' => 'search_event',
                                'request' => json_encode($request->all()),
                                'response' => json_encode($response),
                                'ip' => $request->ip(),
                                'user' => ''
                        ]
                );
                return response()->json($response, $status);
        }

        public function categories(Request $request)
        {

                $types = EventType::has('events')->get()->pluck('name')->toArray();
                $topics = EventTopic::has('events')->get()->pluck('name')->toArray();

                $categories = array_merge($types, $topics);

                $vent_types = strtolower(implode(", ", $categories));


                Logs::create(
                        [
                                'action' => 'get_categories',
                                'request' => json_encode($request->all()),
                                'response' => json_encode($vent_types),
                                'ip' => $request->ip(),
                                'user' => ''
                        ]
                );

                $response = array(
                        'message' => count($categories) . ' categorias listadas.',
                        'status' => $status = 200,
                        'data' => $vent_types,
                );
                return response()->json($response, 200);
        }
}
