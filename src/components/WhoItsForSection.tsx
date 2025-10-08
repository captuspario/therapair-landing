import { Heart, Stethoscope, Building2, HandHeart, TrendingUp } from "lucide-react";
import { Card } from "./ui/card";

export function WhoItsForSection() {
  const audiences = [
    {
      icon: Heart,
      title: "Individuals seeking support",
      description: "LGBTQ+, neurodivergent, and anyone looking for affirming, culturally competent therapy.",
    },
    {
      icon: Stethoscope,
      title: "Therapists & counsellors",
      description: "Join our network of inclusive practitioners and connect with clients who truly fit your expertise.",
    },
    {
      icon: Building2,
      title: "Mental health organizations",
      description: "Partner with us to improve client outcomes and streamline your referral process.",
    },
    {
      icon: HandHeart,
      title: "Supporters & allies",
      description: "Help us build a more inclusive mental health landscape for marginalized communities.",
    },
    {
      icon: TrendingUp,
      title: "Investors & partners",
      description: "Join us in revolutionizing mental healthcare accessibility and quality through better matching.",
    },
  ];

  return (
    <section className="py-16 px-4 sm:px-6 lg:px-8">
      <div className="max-w-6xl mx-auto">
        <div className="text-center mb-12">
          <h2 className="mb-4 text-3xl sm:text-4xl">Who is Therapair for?</h2>
          <p className="text-lg opacity-80 max-w-2xl mx-auto">
            We're building an ecosystem that serves everyone in the mental health space, from seekers to providers to supporters.
          </p>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {audiences.map((audience, index) => (
            <Card key={index} className="p-6 border border-[var(--therapair-rosewood)]/20 bg-white/60 backdrop-blur-sm rounded-2xl hover:shadow-lg hover:bg-white/80 transition-all duration-300 group">
              <div className="w-12 h-12 mb-4 rounded-xl bg-[var(--therapair-calm-clay)]/20 flex items-center justify-center group-hover:bg-[var(--therapair-calm-clay)]/30 transition-colors">
                <audience.icon className="w-6 h-6 text-[var(--therapair-rosewood)]" />
              </div>
              <h3 className="mb-3 text-lg">{audience.title}</h3>
              <p className="opacity-80 text-sm leading-relaxed">{audience.description}</p>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
}