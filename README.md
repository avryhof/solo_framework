# Solo Framework
##### Django-like pieces for Symphony
In my day job, I am a Python/Django developer, and enjoy it very much.  For my side-gig, most of the work calls for
development in PHP on inexpensive shared servers. (CentOS/CPanel) With that in mind, I started looking for a Framework
for PHP that had at least some of the "batteries included" that Django provides.  The ORM, the template system, baked-in
security, forms, Models, URL routing and the other parts I use in my day-to-day work.

## Not a One-to-One Replacement
I can't claim this is a one-to-one drop-in replacement for Django that you can work with in the same exact way while 
just writing PHP instead of Python. There is a lot of functionality that simply cannot be duplicated in the same way it
is done in Django. I also plan to make sure the great features available in PHP are kept in tact, and used where 
possible.

## Ingredients
My goal is to stick to Symphony Components, and libraries that can be installed and managed with Composer, since we 
don't have PIP

##### External Requirements
- Twig
- Silex

##### Provided Here
- Database Connector/Controller
- Object Oriented Database Access (QuerySets)

##### In Progress
- Models
- Decorators
- Symphony Forms Integration (with Twig Integration)
- Symphony Security Component Integration

##### Still to Explore
- Admin

## Disclaimer
This project is not affiliated with the official Django or Symphony projects, and all copyrights and trademarks 
are owned by the respective projects.

## Help
If you want to help with this project, you are welcome to put in Pull requests.